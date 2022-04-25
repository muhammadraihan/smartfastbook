<ul id="js-nav-menu" class="nav-menu">
    <li>
        <a href="{{route('backoffice.dashboard')}}" title="Dashboard" data-filter-tags="dashboard">
            <i class="fal fa-desktop"></i>
            <span class="nav-link-text">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{route('customer.index')}}" title="Customer" data-filter-tags="customer">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">Customer</span>
        </a>
    </li>
    <li>
        <a href="{{route('bank.index')}}" title="Bank" data-filter-tags="bank">
            <i class="fal fa-bank"></i>
            <span class="nav-link-text">Bank</span>
        </a>
    </li>
    <li>
        <a href="{{route('kas.index')}}" title="Kas" data-filter-tags="kas">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">Kas</span>
        </a>
    </li>
    <li>
        <a href="{{route('transaksi.index')}}" title="Transaksi" data-filter-tags="transaksi">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">Transaksi</span>
        </a>
    </li>
    <li>
        <a href="{{route('tariktunai.index')}}" title="TarikTunai" data-filter-tags="tariktunai">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">Tarik Tunai</span>
        </a>
    </li>
    <li>
        <a href="{{route('payment.index')}}" title="Payment" data-filter-tags="payment">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">Payment</span>
        </a>
    </li>
    <li>
        <a href="{{route('ewallet.index')}}" title="E-Wallet" data-filter-tags="ewallet">
            <i class="fal fa-user"></i>
            <span class="nav-link-text">E-Wallet</span>
        </a>
    </li>
    @isset($menu)
    @foreach ($menu as $parent_menu)
    <li class="">
        <a href="{{$parent_menu->route_name ? route($parent_menu->route_name): '#'}}"
            title="{{$parent_menu->menu_title ? $parent_menu->menu_title:''}}">
            <i class="{{$parent_menu->icon_class ? $parent_menu->icon_class:''}}"></i>
            <span class="nav-link-text">{{$parent_menu->menu_title ?$parent_menu->menu_title:''}}</span>
        </a>
        @if (count($parent_menu->childs))
        <ul>
            @include('partials.submenu',['submenu' => $parent_menu->childs])
        </ul>
        @endif
    </li>
    @endforeach
    @endisset
</ul>