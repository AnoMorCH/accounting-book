<?php
echo "
	<header>
		<a href='#'>Санаторий</a>
	</header>
	<input type='checkbox' id='nav' hidden checked />
	<div overflow>
		<ul>
			<li><a href='{$urls["admin_homepage"]}'>Отзывы на проверку</a></li>
			<li><a href='#'>Все отзывы</a></li>
			<li><a href='{$urls["logout_page"]}'>Выход</a></li>
		</ul>
	</div>
";