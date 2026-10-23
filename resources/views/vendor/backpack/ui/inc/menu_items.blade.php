{{-- This file is used for menu items by any Backpack v7 theme --}}
<x-backpack::menu-item title="Dashboard Studio" icon="la la-chart-area" :link="backpack_url('dashboard')" />

<x-backpack::menu-separator title="Catalog & Supply" />
<x-backpack::menu-item title="Products" icon="la la-boxes" :link="backpack_url('product')" />
<x-backpack::menu-item title="Categories" icon="la la-tags" :link="backpack_url('category')" />
<x-backpack::menu-item title="Suppliers / Vendors" icon="la la-truck" :link="backpack_url('supplier')" />

<x-backpack::menu-separator title="Operations & Logistics" />
<x-backpack::menu-item title="Low-Stock PO Draft" icon="la la-file-invoice" :link="route('admin.purchase-order.generate')" />
<x-backpack::menu-item title="Barcode Sticker Studio" icon="la la-barcode" :link="route('product.barcode.sheet')" />
<x-backpack::menu-item title="Activity Audit Logs" icon="la la-history" :link="backpack_url('audit-log')" />
