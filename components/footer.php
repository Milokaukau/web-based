    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-branding">
                <a href="../index.php" class="footer-logo">NOAIR</a>
                <p>Premium hydration solutions for the urban lifestyle.</p>
            </div>
            
            <div class="footer-links-grid">
                <div class="footer-col" style="display: flex; flex-direction: column;">
                    <h4>SHOP</h4>
                    <a href="/pages/shop.php">All Collections</a>
                    <a href="/pages/shop.php?category=1">LiteFlow Series</a>
                    <a href="/pages/shop.php?category=2">Pro Max Series</a>
                </div>
                <div class="footer-col" style="display: flex; flex-direction: column;">
                    <h4>SUPPORT</h4>
                    <a href="/pages/shipping.php">Shipping</a>
                    <a href="/pages/returns.php">Returns</a>
                    <a href="/pages/faq.php">FAQ</a>
                </div>
                <div class="footer-col" style="display: flex; flex-direction: column;">
                    <h4>ABOUT</h4>
                    <a href="/pages/our-story.php">Our Story</a>
                    <a href="/pages/sustainability.php">Sustainability</a>
                    <a href="/pages/careers.php">Careers</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 NOAIR. All rights reserved.</p>
                <div class="social-icons">
                    <span>IG</span>
                    <span>FB</span>
                    <span>TW</span>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
        .site-footer {
            background-color: #fcfcfc;
            padding: 80px 0 40px;
            border-top: 1px solid var(--border-light);
            margin-top: 100px;
            color: var(--text-dark);
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }
        .footer-branding {
            margin-bottom: 50px;
        }
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 6px;
            color: var(--text-dark);
            text-transform: uppercase;
            display: block;
            margin-bottom: 15px;
        }
        .footer-branding p {
            font-size: 0.9rem;
            color: var(--text-muted);
            max-width: 300px;
        }
        .footer-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        .footer-col h4 {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }
        .footer-col a {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 12px;
            transition: color 0.2s;
        }
        .footer-col a:hover {
            color: var(--main-coral);
        }
        .footer-bottom {
            padding-top: 40px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .social-icons {
            display: flex;
            gap: 20px;
            font-weight: 700;
        }
    </style>
</main>
</body>
</html>