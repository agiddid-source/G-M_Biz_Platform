<?php
$ghtHeaderItems = ght_cart_items($ghtProducts);
$ghtHeaderCount = ght_cart_count($ghtHeaderItems);
$ghtCurrentPage = $ghtCurrentPage ?? '';
?>
<header class="ght-site-header">
    <div class="ght-container ght-header-inner">
        <a href="cart.php" class="ght-brand" aria-label="Alamos Generator order">
            <img src="../generator%20logo.jpeg" alt="Alamos Generator logo">
            <span>Alamos Generator<span class="ght-brand-sub">Power equipment and parts</span></span>
        </a>
        <div class="ght-header-assurance"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><span>Order enquiry<small>No online payment</small></span></div>
        <a class="ght-cart-link" href="cart.php"<?= $ghtCurrentPage === 'cart' ? ' aria-current="page"' : '' ?>>
            <span class="ght-cart-icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i><span class="ght-cart-count" aria-label="<?= $ghtHeaderCount ?> items in cart"><?= $ghtHeaderCount ?></span></span>
            <span>Your order</span>
        </a>
    </div>
</header>
