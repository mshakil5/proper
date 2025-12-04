<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

      <li class="nav-item">
        <a href="{{ route('contacts.index') }}" class="nav-link {{ Route::is('contacts.index') ? 'active' : '' }}">
            <i class="fas fa-envelope-open-text nav-icon"></i>
            <p>Contact Messages</p>
        </a>
    </li>

    <li class="nav-item dropdown 
        {{ Route::is('products.*') || Route::is('categories.*') || Route::is('product-tags.*') ? 'menu-open' : '' }}">
        
        <a href="#" class="nav-link dropdown-toggle 
            {{ Route::is('products.*') || Route::is('categories.*') || Route::is('product-tags.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-box"></i>
            <p>
                Products <i class="fas fa-angle-left right"></i>
            </p>
        </a>

        <ul class="nav nav-treeview">
            
            <li class="nav-item">
                <a href="{{ route('products.index') }}" 
                class="nav-link {{ Route::is('products.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram nav-icon"></i>
                    <p>Products</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('allcategories') }}" 
                class="nav-link {{ Route::is('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-th-list nav-icon"></i>
                    <p>Categories</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('producttags.index') }}" 
                class="nav-link {{ Route::is('tags.*') ? 'active' : '' }}">
                    <i class="fas fa-tags nav-icon"></i>
                    <p>Tags</p>
                </a>
            </li>

        </ul>
    </li>

    <li class="nav-item d-none">
        <a href="{{ route('user.index') }}" class="nav-link {{ Route::is('user.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>Users</p>
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{ route('allslider') }}" class="nav-link {{ Route::is('allslider') ? 'active' : '' }}">
            <i class="nav-icon fas fa-sliders-h"></i>
            <p>Sliders</p>
        </a>
    </li>

    <li class="nav-item d-none">
        <a href="{{ route('banner.index') }}" class="nav-link {{ Route::is('banner.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-border-all"></i>
            <p>Banner</p>
        </a>
    </li>

    <li class="nav-item d-none">
        <a href="{{ route('allsubscriptions') }}" class="nav-link {{ Route::is('allsubscriptions') ? 'active' : '' }}">
            <i class="nav-icon fas fa-donate"></i>
            <p>Subscriptions</p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('allplans') }}" class="nav-link {{ Route::is('allplans') ? 'active' : '' }}">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>Plans</p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('allservice') }}" class="nav-link {{ Route::is('allservice') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tools"></i>
            <p>Features</p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('allfeature') }}" class="nav-link {{ Route::is('allfeature') ? 'active' : '' }}">
            <i class="nav-icon fas fa-gem"></i>
            <p>Features</p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('team-members.index') }}" class="nav-link {{ request()->routeIs('team-members.index') ? 'active' : '' }}">
            <i class="fas fa-users nav-icon"></i>
            <p>Team</p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('client-reviews.index') }}" class="nav-link {{ Route::is('client-reviews.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-star"></i>
            <p>Client Reviews</p>
        </a>
    </li>
    <li class="nav-item d-none dropdown {{ Route::is('content.category.index') || Route::is('content.index') || Route::is('tags.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Route::is('content.category.index') || Route::is('content.index') || Route::is('tags.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-images"></i>
            <p>
                Contents <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('content.index', 1) }}" class="nav-link {{ request()->routeIs('content.index') && request()->route('type') == 1 ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>Galleries</p>
                </a>
            </li>
            <li class="nav-item d-none">
                <a href="{{ route('content.index', 2) }}" class="nav-link {{ request()->routeIs('content.index') && request()->route('type') == 2 ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>Blogs</p>
                </a>
            </li>
            <li class="nav-item d-none">
                <a href="{{ route('content.index', 3) }}" class="nav-link {{ request()->routeIs('content.index') && request()->route('type') == 3 ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>Events</p>
                </a>
            </li>
            <li class="nav-item d-none">
                <a href="{{ route('content.index', 4) }}" class="nav-link {{ request()->routeIs('content.index') && request()->route('type') == 4 ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>News</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('content.category.index') }}" class="nav-link {{ Route::is('content.category.index') ? 'active' : '' }}">
                    <i class="fas fa-plus nav-icon"></i>
                    <p>Categories</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tags.index') }}" class="nav-link {{ Route::is('tags.index') ? 'active' : '' }}">
                    <i class="fas fa-tags nav-icon"></i>
                    <p>Tags</p>
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-item dropdown {{ Route::is('admin.companyDetails') || Route::is('admin.company.seo-meta') || Route::is('admin.aboutUs') || Route::is('admin.privacy-policy') || Route::is('admin.terms-and-conditions') || Route::is('allFaq') || Route::is('allcontactemail') || Route::is('allMaster') || Route::is('sections.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('admin.companyDetails') || Route::is('admin.company.seo-meta') || Route::is('admin.aboutUs') || Route::is('admin.privacy-policy') || Route::is('admin.terms-and-conditions') || Route::is('allFaq') || Route::is('allcontactemail') || Route::is('allMaster') || Route::is('sections.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cog"></i>
            <p>
                Settings <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('admin.companyDetails') }}" class="nav-link {{ Route::is('admin.companyDetails') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>Company Details</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.company.seo-meta') }}" class="nav-link {{ Route::is('admin.company.seo-meta') ? 'active' : '' }}">
                    <i class="fas fa-search nav-icon"></i>
                    <p>SEO</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.aboutUs') }}" class="nav-link {{ Route::is('admin.aboutUs') ? 'active' : '' }}">
                    <i class="fas fa-info-circle nav-icon"></i>
                    <p>About Us</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.privacy-policy') }}" class="nav-link {{ Route::is('admin.privacy-policy') ? 'active' : '' }}">
                    <i class="fas fa-user-shield nav-icon"></i>
                    <p>Privacy Policy</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.terms-and-conditions') }}" class="nav-link {{ Route::is('admin.terms-and-conditions') ? 'active' : '' }}">
                    <i class="fas fa-file-contract nav-icon"></i>
                    <p>Terms & Conditions</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allFaq') }}" class="nav-link {{ Route::is('allFaq') ? 'active' : '' }}">
                    <i class="fas fa-question-circle nav-icon"></i>
                    <p>FAQ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.mail-body') }}" class="nav-link {{ Route::is('admin.mail-body') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Mail Body</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allcontactemail') }}" class="nav-link {{ Route::is('allcontactemail') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p>Contact Email</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('sections.index') }}" class="nav-link {{ Route::is('sections.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-sliders-h"></i>
                    <p>Section Settings</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allMaster') }}" class="nav-link {{ Route::is('allMaster') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-th-large"></i>
                    <p>Master Settings</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item" style="margin-top: 100px">
    </li>
  </ul>
</nav>