<?php
    include_once 'classes/PageClass.php';

    $pageContent = '
<div class="bgPattern"></div>
    <header class="header">
        <h1 class="header">Welcome to UIS</h1>
        <p class="header">Your Unified Inventory Management System.</p>
    </header>
    <div class="header">
        <div class="sm-nav">
            <a href="#about">About</a> |
            <a href="#Features">Features</a> |
            <a href="#authors">Authors</a> 
        </div>
    </div>
    <div class="about">
        <section id="about">
            <h2>About UIS</h2>
            <p>UIS is a inventory management site built by Alexander Pellet, Rein Alderfer, Hector Franco, and Drew Urenko. 
                This project is setup to provide a solution to your companies internal inventory needs! 
                From tracking incoming orders of the company. To managing internal inventory under specific categories. 
                And only employees can use the site.
            </p>
        </section>
    </div>
    <div class="Features">
        <section id="Features">
            <h2>Features</h2>
            <table>
                <thead class="thread">
                    <tr>
                        <th class="tableText">Feature</th>
                        <th class="tableText">Explanation</th>
                        <th class="tableText">Sub Features</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Inventory</td>
                        <td>The inventory feature allows you to create, edit, and delete items. 
                        On the dashboard you can  sort the items by category, and add new categries.
                        </td>
                        <td>Create, edit, delete, category sort</td>
                    </tr>
                    <tr>
                        <td>Orders</td>
                        <td>On the orders page you can create new orders, update orders, and delete orders.</td>
                        <td>Create, update, delete</td>
                    </tr>
                    <tr>
                        <td>Users/Account</td>
                        <td>As an admin you can create new users, delete users, and edit users passwords.
                        As any user on the site you can login, logout, update your password, and change your name.</td>
                        <td>Add user, update password, login, logout</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    <div class="authors">
        <section id="authors">
            <h2>Meet the Authors</h2>
            <ul>
                <li class="author-item">
                    <img src="' . $GLOBALS['headshots'] . '/IconMissing.png" alt="Alexander Pellet" class="author-photo" />
                    <div class="author-text"><strong>Alexander Pellet:</strong> Junior Computer Science Major</div>
                </li>
                <li class="author-item">
                    <img src="' . $GLOBALS['headshots'] . '/IconMissing.png" alt="Rein Alderfer" class="author-photo" />
                    <div class="author-text"><strong>Mr. Rein Alderfer:</strong> Junior Information Systems Major</div>
                </li>
                <li class="author-item">
                    <img src="' . $GLOBALS['headshots'] . '/hector.jpeg" alt="Hector Franco" class="author-photo" />
                    <div class="author-text"><strong>Hector Franco:</strong> Junior Computer Science Major</div>
                </li>
                <li class="author-item">
                    <img src="' . $GLOBALS['headshots'] . '/IconMissing.png" alt="Drew Urenko" class="author-photo" />
                    <div class="author-text"><strong>Drew Urenko:</strong> Junior Computer Science Major</div>
                </li>
            </ul>
        </section>
    </div>
    ';
    $page = new PageClass('Home',$pageContent, ['homepage.css'], ['index-animations.js']);
    $page->standardize();
    echo $page->render();
?>
