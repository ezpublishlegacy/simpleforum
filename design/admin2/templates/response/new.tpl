<form action="{concat('/response/new/',$topic_id)|ezurl('no')}" method="post">

    <div class="controlbar" id="controlbar-top">
        <div class="box-bc"><div class="box-ml">
            <div class="button-left">
                <input type="submit" title="{'Create the new response'|i18n('simpleforum/response')}" value="{'Save response'|i18n('simpleforum/response')}" name="NewButton" class="defaultbutton" />
                <input type="submit" title="{'Cancel action'|i18n('simpleforum/response')}" onclick="return confirm( '{'Are you sure you want to cancel?'|i18n('simpleforum/response')}' );" value="{'Cancel response'|i18n('simpleforum/response')}" name="CancelButton" class="button" />
            </div>
            <div class="button-right"></div>
            <div class="float-break"></div>
        </div></div>
    </div>

    {if count($errors)}
        <div class="message-error">
            <h2><span class="time">[{currentdate()|l10n('shortdatetime')}]</span> {'The response could not be saved.'|i18n('simpleforum/response')}</h2>
            <p>{'Required data is either missing or is invalid:'|i18n('simpleforum/response')}</p>
            <ul>
                {foreach $errors as $error}
                    <li>{$error}</li>
                {/foreach}
            </ul>
        </div>
    {/if}
    
    <div class="content-edit">
        <div class="context-block">
            
            <div class="box-header">
                <h1 class="context-title">{'Create new response'|i18n('simpleforum/response')}</h1>
                <div class="header-mainline"></div>
            </div>

            <div class="box-content">
                <div class="context-information">
                    <p class="right translation">
                        {$topic.language_object.name}&nbsp;<img width="18" height="12" alt="{$topic.language_object.locale}" style="vertical-align: middle;" src={$topic.language_object.locale|flag_icon} />
                    </p>
                    <div class="break"></div>
                </div>

                <div class="context-attributes">
                    
                    <div class="block ezcca-edit-datatype-ezstring simpleforum-response-edit-name">
                        <label>{'Name'|i18n('simpleforum/response')} <span class="required">({'required'|i18n('simpleforum/response')})</span></label>
                        <input type="text" value="{$name}" name="name" size="70" class="box simpleforum-response simpleforum-response_name" id="simpleforum-response_name" />
                    </div>
                    
                    <div class="block ezcca-edit-datatype-ezstring simpleforum-response-edit-content">
                        <label>{'Content'|i18n('simpleforum/response')} <span class="required">({'required'|i18n('simpleforum/response')})</span></label>
                        <textarea name="content" size="70" class="box simpleforum-response simpleforum-response_content" id="simpleforum-response_content">{$content}</textarea>
                    </div>
                    
                </div>
            </div>
            
            <div class="controlbar">
                <div class="block">
                    <input type="submit" title="{'Create the new response'|i18n('simpleforum/response')}" value="{'Save response'|i18n('simpleforum/response')}" name="NewButton" class="defaultbutton">
                    <input type="submit" title="{'Cancel action'|i18n('simpleforum/response')}" onclick="return confirm( '{'Are you sure you want to cancel?'|i18n('simpleforum/response')}' );" value="{'Cancel response'|i18n('simpleforum/response')}" name="CancelButton" class="button">
                </div>
            </div>
        </div>
    </div>
    
</form>
