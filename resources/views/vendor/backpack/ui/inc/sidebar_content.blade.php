// resources/views/vendor/backpack/base/inc/sidebar_content.blade.php
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <li class="nav-item">
            <a href="{{ backpack_url('dashboard') }}" class="nav-link">
                <i class="nav-icon las la-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <!-- Products Section -->
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon las la-box"></i>
                <p>
                    Products Management
                    <i class="right las la-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ backpack_url('product') }}" class="nav-link">
                        <i class="las la-list nav-icon"></i>
                        <p>All Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ backpack_url('product/create') }}" class="nav-link">
                        <i class="las la-plus nav-icon"></i>
                        <p>Add New Product</p>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Categories Section -->
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon las la-tags"></i>
                <p>
                    Categories
                    <i class="right las la-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ backpack_url('category') }}" class="nav-link">
                        <i class="las la-list nav-icon"></i>
                        <p>All Categories</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ backpack_url('category/create') }}" class="nav-link">
                        <i class="las la-plus nav-icon"></i>
                        <p>Add New Category</p>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Settings -->
        <li class="nav-item">
            <a href="{{ backpack_url('user') }}" class="nav-link">
                <i class="nav-icon las la-users"></i>
                <p>Users</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ backpack_url('logout') }}" class="nav-link">
                <i class="nav-icon las la-sign-out-alt"></i>
                <p>Logout</p>
            </a>
        </li>

    </ul>
</nav>