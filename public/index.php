<?php
    include_once 'components/product_card.php';
    require_once 'components/database.php';

    $connection = new DatabaseConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mick's Webshop - Cheap laptops & phones</title>
    <?php require_once 'components/base_head.php' ?>
    <link rel="stylesheet" href="scss/pages/main.css">
</head>
<body>
    <?php include_once 'components/header.php' ?>
    <main class="main">
        <div class="main__hero-wrapper">
            <div class="main__hero-wrapper__content">
                <p class="main__hero-wrapper__content__hero-text">Discover the latest Laptops and Phones: Quality, Innovation, and Great Prices await you.</p>
                <a class="main__hero-wrapper__content__show-now" href="search.php">Shop Now</a>
            </div>
            <a class="main__hero-wrapper__scroll-btn" href="#sales-section">
                <svg class="bottom-header__nav-item__name__arrow" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 -960 960 960" fill="currentColor"><path d="m480-555.69-184 184L267.69-400 480-612.31 692.31-400 664-371.69l-184-184Z"/></svg>
            </a>
        </div>
        <section class="main__sales" id="sales-section">
            <h2 class="main__sales__title">Products On Sale:</h2>
            <div class="main__sales__products"><?php
                // Fetch 4 random on-sale products from the database
                $results = $connection->fetch('SELECT * FROM product WHERE onsale = 1 ORDER BY RAND() LIMIT 4');
                $products = $results->fetch_all(MYSQLI_ASSOC);

                // Render the product cards
                foreach ($products as $product) {
                    render_product_card($product);
                }
            ?></div>
        </section>
        <section class="main__recents">
            <h2 class="main__recents__title">Recently Visited:</h2>
            <div class="main__recents__products"><?php
                // Get the recently visited products
                $recents = json_decode($_COOKIE['recents']);
                if (gettype($recents) === 'array' && count($recents) > 0) {
                    // Construct the query
                    $wheres = ''; $binds = '';
                    foreach ($recents as $index => $id) {
                        if ($index > 0) $wheres .= ' OR ';
                        $wheres .= 'id = ?';
                        $binds .= 'i';
                    }
                    $query = "SELECT * FROM product WHERE $wheres";

                    // Fetch them from the database
                    $products = [];
                    $results = $connection
                        ->fetch($query, $binds, ...$recents)
                        ->fetch_all(MYSQLI_ASSOC);

                    foreach ($results as $product) {
                        $products[$product['id']] = $product;
                    }

                    // Render the product cards
                    foreach ($recents as $id) {
                        render_product_card($products[$id]);
                    }
                }
            ?></div>
        </section>
    </main>
    <?php include_once 'components/footer.php' ?>
</body>
</html>