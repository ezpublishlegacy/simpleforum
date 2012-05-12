<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of simpleforumtopiccollection
 *
 * @author jobou
 */
class SimpleForumCollection {
    
    function fetchTopic( $id )
    {
        $result = SimpleForumTopic::fetch($id);
        return array( 'result' => $result );
    }
    
    function fetchTopicList( $forumNodeId, $depth, $limit, $offset, $sortBy, $asObject, $attributeFilter, $limitation, $language )
    {
        $filter  = array();
        
        $forumNode = eZContentObjectTreeNode::fetch($forumNodeId);
    	if (!SimpleForumTools::checkAccess($forumNode) && !is_array($limitation))
    	{
        	return array( 'result' => array() );
    	}
    	
        $filter['node_id'] = $this->getForumNodeIds($forumNodeId, $depth, $limitation);
        
        $this->formatAttributeFilter($attributeFilter, $filter);
        
        $formatedSortBy = $this->formatSortBy($sortBy);
        
        $formatedLimit = null;
        if ($limit)
        {
            $formatedLimit = array(
                'offset' => $offset,
                'length' => $limit
            );
        }
        
        $result = SimpleForumTopic::fetchList( $filter, $formatedLimit, $formatedSortBy, $asObject );
        
        return array( 'result' => $result );
    }
    
    public function fetchTopicCount( $forumNodeId, $depth, $attributeFilter, $limitation, $language )
    {
        $filter  = array();
        
        $forumNode = eZContentObjectTreeNode::fetch($forumNodeId);
        if (!SimpleForumTools::checkAccess($forumNode) && !is_array($limitation))
        {
        	return array( 'result' => 0 );
        }
        
        $filter['node_id'] = $this->getForumNodeIds($forumNodeId, $depth, $limitation);
        
        $this->formatAttributeFilter($attributeFilter, $filter);
        
        $result = SimpleForumTopic::fetchCount( $filter );
        
        return array( 'result' => $result );
    }
    
    function fetchResponse( $id )
    {
        $result = SimpleForumResponse::fetch($id);
        return array( 'result' => $result );
    }
    
    function fetchResponseList($topicID, $limit, $offset, $sortBy, $asObject, $attributeFilter, $limitation)
    {
        $filter  = array();
        
        $topic = SimpleForumTopic::fetch($topicID);
        if ( !$topic || 
             (!$topic->canRead() && !is_array($limitation)) )
        {
        	return array( 'result' => array() );
        }
        
        $filter['topic_id'] = array(array($topicID));
        
        $this->formatAttributeFilter($attributeFilter, $filter);
        
        $formatedSortBy = $this->formatSortBy($sortBy);
        
        $formatedLimit = null;
        if ($limit)
        {
            $formatedLimit = array(
                'offset' => $offset,
                'length' => $limit
            );
        }
        
        $result = SimpleForumResponse::fetchList( $filter, $formatedLimit, $formatedSortBy, $asObject );
        
        return array( 'result' => $result );
    }
    
    public function fetchResponseCount( $topicID, $attributeFilter, $limitation )
    {
        $filter  = array();
        
        $topic = SimpleForumTopic::fetch($topicID);
        if ( !$topic ||
             (!$topic->canRead() && !is_array($limitation)) )
        {
        	return array( 'result' => 0 );
        }
        
        $filter['topic_id'] = array(array($topicID));
        
        $this->formatAttributeFilter($attributeFilter, $filter);
        
        $result = SimpleForumResponse::fetchCount( $filter );
        
        return array( 'result' => $result );
    }
    
    public function formatAttributeFilter($attributeFilter, &$filter)
    {
        if (is_array($attributeFilter) && isset($attributeFilter[0]) && is_array($attributeFilter[0]))
        {
            foreach ($attributeFilter as $filterItem)
            {
                if (is_array($filterItem) && isset($filterItem[0]) && count($filterItem) == 3)
                {
                    if ($filterItem[1] == '=')
                    {
                        $filter[$filterItem[0]] = $filterItem[2];
                    }
                    else
                    {
                        $filter[$filterItem[0]] = array($filterItem[1], $filterItem[2]);
                    }
                }
            }
        }
        elseif (is_array($attributeFilter) && isset($attributeFilter[0]) && count($attributeFilter) == 3)
        {
            if ($attributeFilter[1] == '=')
            {
                $filter[$attributeFilter[0]] = $attributeFilter[2];
            }
            else
            {
                $filter[$attributeFilter[0]] = array($attributeFilter[1], $attributeFilter[2]);
            }
        }
    }
    
    public function formatSortBy($sortBy)
    {
        $formatedSortBy = null;
        if (is_array($sortBy))
        {
            if (is_array($sortBy[0]))
            {
                foreach ($sortBy as $sortItem)
                {
                    $formatedSortBy[$sortItem[0]] = $sortItem[1];
                }
            }
            else
            {
                $formatedSortBy[$sortBy[0]] = $sortBy[1];
            }
        }
        
        return $formatedSortBy;
    }
    
    public function getForumNodeIds($forumNodeId, $depth, $limitation = false)
    {
        $nodeIDs = array($forumNodeId);
        if ($depth != 1)
        {
            $forums = eZContentObjectTreeNode::subTreeByNodeID(array('Depth'=>$depth), $forumNodeId);
            foreach ($forums as $forum)
            {
            	if (SimpleForumTools::checkAccess($forum) || is_array($limitation))
            	{
                	$nodeIDs[] = $forum->attribute('node_id');
            	}
            }
        }
        return array($nodeIDs);
    }
    
    public function searchTopic( $query, $forumNodeId, $limit, $offset, $sortBy, $attributeFilter, $language )
    {
        $parameters = array();
        if ( $limit !== false)
            $parameters['limit'] = $limit;
        
        if ( $offset !== false)
            $parameters['offset'] = $offset;
        
        if ( $forumNodeId !== false)
            $parameters['parent_node_id'] = $forumNodeId;
        
        if ( is_array($sortBy) && !empty($sortBy) )
            $parameters['sort_by'] = $sortBy;
        
        if (is_array($attributeFilter) && !empty($attributeFilter))
        {
            if (!is_array($attributeFilter[0]))
            {
                $parameters['attribute_filter'] = array($attributeFilter);
            }
            else
           {
               $parameters['attribute_filter'] = $attributeFilter;
            }
        }
        
        $searchResult = array();
        if ($engine = simpleForumSearch::instance()->getEngine())
        {
            $searchResult = $engine->search( $query,
                    $parameters,
                    'simpleForumTopicSearch' );
        }
        
        return array( 'result' => $searchResult );
    }
    
    public function searchResponse( $query, $topicId, $limit, $offset, $sortBy, $attributeFilter )
    {
        $parameters = array();
        if ( $limit !== false)
            $parameters['limit'] = $limit;
    
        if ( $offset !== false)
            $parameters['offset'] = $offset;
    
        if ( $topicId !== false)
            $parameters['parent_node_id'] = $topicId;
    
        if ( is_array($sortBy) && !empty($sortBy) )
            $parameters['sort_by'] = $sortBy;
    
        if (is_array($attributeFilter) && !empty($attributeFilter))
        {
            if (!is_array($attributeFilter[0]))
            {
                $parameters['attribute_filter'] = array($attributeFilter);
            }
            else
            {
                $parameters['attribute_filter'] = $attributeFilter;
            }
        }
    
        $searchResult = array();
        if ($engine = simpleForumSearch::instance()->getEngine())
        {
            $searchResult = $engine->search( $query,
                    $parameters,
                    'simpleForumResponseSearch' );
        }
    
        return array( 'result' => $searchResult );
    }
}

?>
