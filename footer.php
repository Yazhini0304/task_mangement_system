
    <style>
        :root {
            --custom-blue: #424fa3; 
        }
        .bg-light.footer-override {
            background: linear-gradient(135deg, var(--custom-blue) 0%, #e0f7fa 100%) !important;
            color: #ffffff !important; 
            border-top: none !important; 
        }
        
        .bg-light.footer-override .text-center {
            color: #ffffff !important;
        }

    </style>

    <footer class="bg-light text-center text-lg-start mt-5 footer-override">
        <div class="text-center p-3">
            © <?= date('Y') ?> Task Management System
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>