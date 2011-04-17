<table class="versionInfo">
<tbody>
<?php foreach ($plugins as $category => $categoryPlugins): ?>
<tr>
<th colspan="4">
<?php if ('authentication' === $category): ?>
認証プラグイン
<?php elseif ('skin' === $category): ?>
スキンプラグイン
<?php else: ?>
その他
<?php endif ?>
</th>
</tr>

<?php foreach ($categoryPlugins as $pluginName => $pluginInfo): ?>
<tr>
<td><?php echo $pluginName ?></td>
<td><?php echo $pluginInfo['version'] ?></td>
<td><?php echo $pluginInfo['summary'] ?></td>
<td><?php echo $pluginInfo['developers'] ?></td>
</tr>
<?php endforeach ?>

<?php endforeach ?>
</tbody>
</table>
