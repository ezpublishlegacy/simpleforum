<?php

/**
 * @file
 * Define the search engine for solr and ezfind extension
 * Solr must used the multi core configuration. See documentation for more information
 *
 * @author jobou
 * @package simpleforum
 */
class simpleForumSolr implements ezpSearchEngine
{
    /**
     * Load the solr.ini settings
     * 
     * @var eZINI
     */
    var $SolrINI;
    
    /**
     * solr search handler
     * 
     * @var ezcSearchSolrHandler
     */
    var $handler;
    
    /**
     * solr search manager
     * 
     * @var ezcSearchEmbeddedManager
     */
    var $manager;
    
    /**
     * solr search session
     * 
     * @var ezcSearchSession
     */
    var $session;
    
    /**
     * Constructor
     * 
     * Instanciate solr search
     * Start a transaction
     */
    function __construct()
    {
        $this->SolrINI = eZINI::instance( 'solr.ini' );

        $host = $this->SolrINI->variable('ForumSolrBase', 'SearchServerHost');
        $port = $this->SolrINI->variable('ForumSolrBase', 'SearchServerPort');
        $path = $this->SolrINI->variable('ForumSolrBase', 'SearchServerPath');
        
        $this->handler = new ezcSearchSolrHandler( $host, $port, $path );
        $this->manager = new ezcSearchEmbeddedManager;
        $this->session = new ezcSearchSession( $this->handler, $this->manager );
        
        $this->handler->beginTransaction();
    }
    
	/**
	 * Whether a commit operation is required after adding/removing objects.
	 *
	 * @see commit()
	 * 
	 * @return bool
	 */
	public function needCommit()
	{
		return true;
	}
	
	/**
	 * Whether calling removeObject() is required when updating an object.
	 *
	 * @see removeObject()
	 * 
	 * @return bool
	 */
	public function needRemoveWithUpdate()
	{
		return false;
	}
	
	/**
	 * Adds object $object to the search database.
	 *
	 * @param eZContentObject $object 
	 *   Object to add to search engine
	 * @param boolean         $commit 
	 *   Whether to commit after adding the object
	 * 
	 * @return bool
	 */
	public function addObject( $object, $commit = true )
	{
        $doc = $object->getSearchObject();        
        $this->session->index( $doc );
        
        // Reconnect at each update because of a reset connection problem
        $this->handler->reConnect();
        
        return true;
	}
	
	/**
	 * Removes object $contentObject from the search database.
	 *
	 * @param eZContentObject $contentObject 
	 *   the content object to remove
	 * @param bool            $commit 
	 *   Whether to commit after removing the object
	 *   
	 * @return bool
	 */
	public function removeObject( $contentObject, $commit = true )
	{
		return true;
	}
	
	/**
	 * Searches $searchText in the search database.
	 *
	 * @see supportedSearchTypes()
	 * 
	 * @param string $searchText  
	 *   Search term
	 * @param array  $params      
	 *   Search parameters
	 * @param array  $searchTypes 
	 *   Search types
	 *   
	 * @return array
	 */
	public function search( $searchText, $params = array(), $searchTypes = array() )
	{
	    if (is_array($searchTypes))
	    {
	        $searchTypes = $searchTypes[0];
	    }
	    
	    $limit = 10;
	    if (isset($params['limit']))
	    {
	        $limit = $params['limit'];
	    }
	    
	    $offset = 0;
	    if (isset($params['offset']))
	    {
	        $offset = $params['offset'];
	    }
	    
	    // initialize a pre-configured query
	    $q = $this->session->createFindQuery( $searchTypes );
	    $q->where($searchText);
	    $q->limit( $limit , $offset );
	    $q->highlight();
	    
	    if (isset($params['parent_node_id']))
	    {
	        $q->where( $q->eq('parent_id', $params['parent_node_id']) );
	    }
	    
	    if (isset($params['attribute_filter']) && is_array($params['attribute_filter']))
	    {
	        foreach ($params['attribute_filter'] as $filter)
	        {
	            if (is_array($filter))
	            {
	                $q->where( $q->eq($filter[0], $filter[1]) );
	            }
	        }
	    }
	    
	    if (isset($params['sort_by']))
	    {
	        $order = ($params['sort_by'][1] == 'desc') ? ezcSearchQueryTools::DESC : ezcSearchQueryTools::ASC;
	        $q->orderBy( $params['sort_by'][0], $order );
	    }
	    
	    // run the query and show titles for found documents
	    $r = $this->session->find( $q );
	    
	    $result = array();
	    foreach( $r->documents as $res )
	    {
	        $result[] = $res->document;
	    }
	    
		return $result;
	}
	
	/**
	 * Returns an array describing the supported search types by the search engine.
	 * 
	 * @return array
	 */
	public function supportedSearchTypes()
	{
		return array(
		    simpleForumResponseSearch::SEARCH_TYPE,
		    simpleForumTopicSearch::SEARCH_TYPE        
		);
	}
	
	/**
	 * Commit the changes made to the search engine.
	 *
	 * @see needCommit()
	 */
	public function commit()
	{
	    // Reconnect at each update because of a reset connection problem
	    $this->handler->reConnect();
	    
	    $this->handler->commit();
	}
	
	/**
	 * Clean entire index
	 *
	 * @see needCommit()
	 */
	public function cleanUp()
	{    
        $deleteResponse = $this->session->createDeleteQuery('simpleForumResponseSearch');
        $this->handler->delete( $deleteResponse );
        
        $deleteTopic = $this->session->createDeleteQuery('simpleForumTopicSearch');
        $this->handler->delete( $deleteTopic );
	}
}