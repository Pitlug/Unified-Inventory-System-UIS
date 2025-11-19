<?php
    include_once 'classes/PageClass.php';
    $pageContent = '
    <html>
<head></head>
<body>
    <header>
        <h1>Welcome to UIS</h1>
        <p>Your unified inventory management system.</p>
    </header>
    <nav>
        <a href="#about">About</a> |
        <a href="#Features">Features</a> |
        <a href="#authors">Authors</a> |
        <a href="#contact">Contact</a>
    </nav>
    <section id="about">
        <h2>About UIS</h2>
        <p>UIS is a inventory management site built by Alexander Pellet, Rein Alderfer, Hector Franco, and Drew Urenko. 
        This project is setup to provide a solution to your companies internal inventry needs! 
        From traking incoming orders of the company. To managing internal inventory under specific categories. 
        And only employs can use the site.
        </p>
    </section>
    <section id="Features">
        <h2>Features</h2>
        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Explanation</th>
                    <th>Sub Features</th>
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
    <section id="authors">
        <h2>Meet the Authors</h2>
        <ul>
            <li><strong>Mr. Alexander Pellet:</strong> Junior Computer Science major</li>
            <li><strong>Mr.Rein Alderfer:</strong> Junior Information Systems Major</li>
            <li><strong>Mr. Hector Franco:</strong> Junior Computer Science Major</li>
            <li><strong>Mr. Drew Urenko:</strong> Junior Computer Science Major</li>
        </ul>
    </section>
    <section id="contact">
        <h2>Contact Us to Sign Up Today!</h2>
        <form>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name"><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email"><br><br>
            <label for="message">Company Name:</label><br>
            <textarea id="message" name="message" rows="4"></textarea><br><br>
            <button type="submit">Send</button>
        </form>
    </section>
</body>
</html>
    ';
    $page = new PageClass('Home',$pageContent);
    $page->standardize();
    echo $page->render();
?>
