<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Your Name">
    <meta name="description" content="Enhancements implemented for COS10032 Assignment Part 1">
    <title>Enhancements</title>
    <link href="style.css" rel="stylesheet"/>
</head>

<body>

<header class="site-header">
    <h1>Enhancements Implemented</h1>
    <div class="logo">EZ-Accounting</div>
    <?php include 'nav.inc';?>
</header>

<main>

    <section>
        <h2>Overview</h2>
        <p>
            This page describes two enhancements I contributed to the group website.  
            These enhancements go beyond the base assignment requirements and demonstrate advanced CSS techniques, including responsive design, hover interactions, and smooth transitions.  
            Each feature includes an explanation of how it works, why it qualifies as an enhancement, the code used, and a hyperlink to where the enhancement is applied.
        </p>
    </section>


    <!-- Enhancement 1 -->
    <section>
        <h2>Enhancement 1: Responsive Design (CSS Media Queries)</h2>

        <p>
            To make the website usable on mobile phones and tablets, I implemented <strong>responsive design</strong> using CSS media queries.
            Responsive design is not required by the base assignment, and therefore qualifies as a valid enhancement.
            This feature automatically adjusts layout, fonts, and element widths depending on screen size, ensuring that the site remains readable and functional on smaller devices.
        </p>

        <h3>How This Enhancement Extends the Base Requirements</h3>
        <ul>
            <li>The original assignment does not require mobile-friendly behaviour.</li>
            <li>Media queries are a more advanced CSS3 feature not covered in tutorials.</li>
            <li>Float-based layouts (such as the <code>&lt;aside&gt;</code> on <a href="product.html">product.html</a>) would normally break on small screens.</li>
        </ul>

        <h3>Code Used</h3>
        <pre>
@media (max-width: 600px) {
    nav ul {
        flex-direction: column;
        text-align: center;
    }

    aside {
        width: 100%;
        float: none;
        margin-top: 20px;
    }

    body {
        font-size: 16px;
        padding: 10px;
    }
}
        </pre>

        <h3>Meaning and Function of Each Part</h3>
        <ul>
            <li><strong>@media (max-width: 600px)</strong> – Applies the enclosed CSS only when the screen is 600px wide or smaller (mobile breakpoint).</li>
            <li><strong>nav ul { flex-direction: column; }</strong> – Stacks menu items vertically to prevent overcrowding.</li>
            <li><strong>aside { width: 100%; float: none; }</strong> – Makes the sidebar fit correctly on mobile.</li>
            <li><strong>body { font-size: 16px; }</strong> – Increases readability on small screens.</li>
        </ul>

        <p><strong>Where This Enhancement Appears:</strong></p>
        <ul>
            <li><a href="product.html">product.html</a> – Responsive aside and layout</li>
            <li><a href="index.html">index.html</a> – Responsive menu</li>
            <li>All pages automatically inherit mobile layout through shared CSS</li>
        </ul>
    </section>


    <!-- Enhancement 2 -->
    <section>
        <h2>Enhancement 2: Hover Effects & Smooth Transitions</h2>

        <p>
            To improve interactivity and usability, I implemented hover effects and smooth CSS transitions on navigation links and interactive elements.
            These enhancements give users visual feedback when hovering over links, making the interface feel modern and responsive.
            They also go beyond the basic style requirements of the assignment.
        </p>

        <h3>How This Enhancement Extends the Base Requirements</h3>
        <ul>
            <li>Hover animations and transitions are not required in the assignment.</li>
            <li>Transitions introduce CSS3 behaviour not covered in tutorials.</li>
            <li>It improves usability by showing which menu item is being hovered.</li>
        </ul>

        <h3>Code Used</h3>
        <pre>
nav a {
    color: white;
    padding: 10px 15px;
    transition: 0.3s ease;
}

nav a:hover {
    background-color: #ff9900;
    color: black;
}
        </pre>

        <h3>Meaning and Function of Each Part</h3>
        <ul>
            <li><strong>nav a</strong> – Selects all navigation links.</li>
            <li><strong>transition: 0.3s ease;</strong> – Smoothly animates changes over 0.3 seconds.</li>
            <li><strong>nav a:hover</strong> – Styles applied when the user hovers the mouse over a link.</li>
            <li><strong>background-color</strong> – Highlights the link on hover.</li>
            <li><strong>color: black</strong> – Ensures readable text on a highlighted background.</li>
        </ul>

        <p><strong>Where This Enhancement Appears:</strong></p>
        <ul>
            <li><a href="index.html">index.html</a> – Hover effects visible on navigation bar</li>
            <li>All pages using the shared navigation menu inherit this behaviour</li>
        </ul>
    </section>

</main>

<footer>
    <p>&copy; 2025 COS10032 Assignment – Enhancements Page</p>
</footer>

</body>
</html>




