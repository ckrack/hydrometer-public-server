<a class="navbar-brand mb-0" href="/ui/"><?=getenv('SITE_TITLE')?></a>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="/ui/"><?=_('Spindles')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/ui/fermentations"><?=_('Fermentations')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/ui/data"><?=_('Datapoints')?></a>
        </li>
        <li class="nav-item ml-2">
            <a class="nav-link text-muted" href="/guide"><?=_('Guide')?></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="navbar-text text-muted mr-3">User: <?=$user->getUsername()?></li>
        <li class="nav-item">
            <a class="nav-link" href="/ui/settings"><?=_('Settings')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-danger" href="/auth/logout"><?=_('Logout')?></a>
        </li>
    </ul>
</div>
