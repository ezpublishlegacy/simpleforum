<tr>
    <td><a href="{concat('/topic/view/',$topic.id)|ezurl('no')}">{$topic.name}</a></td>
    <td>{$topic.modified|l10n('shortdatetime')}</td>
    <td>{$topic.view_count}</td>
    <td>{$topic.response_count}</td>
</tr>