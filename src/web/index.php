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
        <a href="#speakers">Speakers</a> |
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
                    <th>Inventory</th>
                    <th>Orders</th>
                    <th>Users</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>9:00 AM</td>
                    <td>Opening Keynote</td>
                    <td>GeeksforGeeks Coding Plateform</td>
                </tr>
                <tr>
                    <td>10:30 AM</td>
                    <td>Understanding AI and Machine Learning</td>
                    <td>Mr. Arvind Kumar</td>
                </tr>
                <tr>
                    <td>1:00 PM</td>
                    <td>Lunch Break</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>2:00 PM</td>
                    <td>Exploring the Future of Cloud Computing</td>
                    <td>Ms. Neha Gupta</td>
                </tr>
            </tbody>
        </table>
    </section>
    <section id="speakers">
        <h2>Meet the Speakers</h2>
        <ul>
            <li><strong>Dr. Radhika Sharma:</strong> AI Expert and Researcher</li>
            <li><strong>Mr. Arvind Kumar:</strong> Senior Data Scientist at TechWave</li>
            <li><strong>Ms. Neha Gupta:</strong> Cloud Computing Specialist at CloudTech</li>
            <li><strong>Mr. Sandeep Reddy:</strong> Full Stack Developer and Open-Source Contributor</li>
        </ul>
    </section>
    <section id="contact">
        <h2>Contact Us</h2>
        <form>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name"><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email"><br><br>
            <label for="message">Message:</label><br>
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
