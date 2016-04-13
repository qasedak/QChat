<body>
<div style="margin: 0 0 0 30px;">{tr('Login information')}</div>
{% foreach ($passwords as $p): %}
	<div style="margin: 0 0 0 60px;"><b>{tr('Name')}</b>: {$p['name']}</div>
	<div style="margin: 0 0 0 60px;"><b>{tr('Password')}</b>: {$p['password']}</div>
	<br>
{% endforeach; %}
<br>
<a href="{Elf::Settings('chat_url')}" target="_black">{tr('Entry to chat')}</a>
</body>
