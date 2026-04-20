</div><!-- /.admin-body -->

<footer class="admin-footer" style="background: var(--bg-card, #fff); border-top: 1px solid var(--border-card, #eee); padding: 30px 40px; margin-top: auto;">
    <div class="admin-footer-container" style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
        
        <div class="admin-footer-main" style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px;">
            
            <!-- Branding & Status -->
            <div class="admin-footer-brand" style="flex: 1; min-width: 250px;">
                <h3 style="margin: 0 0 10px 0; color: var(--text-dark, #111); font-size: 1.2rem; font-weight: 700;">NOAIR Admin Portal</h3>
                <p style="color: var(--text-muted, #666); font-size: 0.85rem; margin: 0 0 15px 0;">Central management system for NOAIR inventory, orders, and user administration.</p>
                <div style="font-size: 0.8rem; color: var(--text-muted, #666); display: flex; align-items: center; gap: 6px;">
                    <span style="color: #10B981;">●</span> All Systems Operational
                </div>
            </div>

            <!-- Quick Internal Links -->
            <div class="admin-footer-links" style="flex: 1; min-width: 200px;">
                <h4 style="margin: 0 0 15px 0; color: var(--text-dark, #111); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Resources</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                    <li><a href="#" style="color: var(--text-muted, #666); text-decoration: none;">Internal Documentation</a></li>
                    <li><a href="#" style="color: var(--text-muted, #666); text-decoration: none;">Contact IT Support</a></li>
                    <li><a href="#" style="color: var(--text-muted, #666); text-decoration: none;">View Activity Logs</a></li>
                </ul>
            </div>

            <!-- System Info -->
            <div class="admin-footer-sysinfo" style="flex: 1; min-width: 200px;">
                <h4 style="margin: 0 0 15px 0; color: var(--text-dark, #111); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">System Info</h4>
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem; color: var(--text-muted, #666);">
                    <div><strong>Version:</strong> 1.0.0 (Production)</div>
                    <div><strong>Server Time:</strong> <?= date('Y-m-d H:i:s') ?></div>
                    <div><strong>Timezone:</strong> Asia/Kuala_Lumpur</div>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright Bar -->
        <div class="admin-footer-bottom" style="border-top: 1px solid var(--border-light, #f0f0f0); padding-top: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-muted, #888);">
            <p style="margin: 0;">&copy; <?= date('Y') ?> NOAIR. All rights reserved.</p>
            <p style="margin: 0; font-weight: 500;">Secured with 256-bit Encryption</p>
        </div>

    </div>
</footer>

</div><!-- /.admin-shell -->