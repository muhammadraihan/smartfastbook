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
        <a href="{{route('kas.index')}}" title="Kas" data-filter-tags="kas">
            <i class="fal fa-credit-card"></i>
            <span class="nav-link-text">Kas</span>
        </a>
    </li>
    <li>
        <a href="#" title="Transaksi" data-filter-tags="Transaksi">
            <i class="fal fa-exchange"></i>
            <span class="nav-link-text">Transaksi</span>
        </a>
        <ul>
            <li>
                <a href="{{route('transaksi.index')}}" title="Transfer" data-filter-tags="transfer">
                    <i class="fal fa-exchange"></i>
                    <span class="nav-link-text">Transfer Bank</span>
                </a>
            </li>
            <li>
                <a href="{{route('tariktunai.index')}}" title="TarikTunai" data-filter-tags="tariktunai">
                    <i class="fal fa-money-bill-alt"></i>
                    <span class="nav-link-text">Tarik Tunai</span>
                </a>
            </li>
            <li>
                <a href="{{route('ewallet.index')}}" title="E-Wallet" data-filter-tags="ewallet">
                    <i class="fal fa-shopping-cart"></i>
                    <span class="nav-link-text">Top Up E-Wallet</span>
                </a>
            </li>
        </ul>
    </li>
    @hasanyrole('superadmin|admin')
    <li>
        <a href="#" title="Saldo" data-filter-tags="saldo">
            <i class="fal fa-inbox-out"></i>
            <span class="nav-link-text">Saldo</span>
        </a>
        <ul>
            <li>
                <a href="{{route('saldo.index')}}" title="Saldo" data-filter-tags="saldo">
                    <i class="fal fa-inbox-in"></i>
                    <span class="nav-link-text">Saldo Masuk</span>
                </a>
            </li>
            <li>
                <a href="{{route('saldoKeluar.index')}}" title="SaldoKeluar" data-filter-tags="saldoKeluar">
                    <i class="fal fa-inbox-out"></i>
                    <span class="nav-link-text">Saldo Keluar</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="#" title="PengaturanData" data-filter-tags="pengaturan data">
            <i class="fal fa-database"></i>
            <span class="nav-link-text">Pengaturan Data</span>
        </a>
        <ul>
            <li>
                <a href="{{route('bank.index')}}" title="Bank" data-filter-tags="bank">
                    <i class="fal fa-dollar-sign"></i>
                    <span class="nav-link-text">Bank</span>
                </a>
            </li>
            <li>
                <a href="{{route('payment.index')}}" title="Payment" data-filter-tags="payment">
                    <i class="fal fa-shopping-bag"></i>
                    <span class="nav-link-text">Dompet Online</span>
                </a>
            </li>
            <li>
                <a href="{{route('perusahaan.index')}}" title="Perusahaan" data-filter-tags="perusahaan">
                    <i class="fal fa-building"></i>
                    <span class="nav-link-text">Perusahaan</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{route('rekening.index')}}" title="Rekening" data-filter-tags="rekening">
                    <i class="fal fa-usd-circle"></i>
                    <span class="nav-link-text">Rekening</span>
                </a>
            </li> --}}
        </ul>
    </li>
    @endhasanyrole
    @hasanyrole('superadmin')
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
    @endhasanyrole
</ul>