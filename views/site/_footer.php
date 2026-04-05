<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<footer class="mt-auto py-8 border-t border-slate-200">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-sm font-medium">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-slate-100 rounded-lg">
                <i data-lucide="info" class="w-4 h-4 text-slate-500"></i>
            </div>
            <span>© <?= date('Y') ?> Buached Hospital Information System</span>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 hover:text-hos-blue transition-colors cursor-default">
                <i data-lucide="database" class="w-4 h-4"></i>
                <span>HOSxP MariaDB Connection: <span class="text-green-500 font-bold italic">Online</span></span>
            </div>
            <div class="flex items-center gap-2 hover:text-hos-blue transition-colors cursor-default">
                <i data-lucide="cpu" class="w-4 h-4"></i>
                <span>Render: <?= round(Yii::getLogger()->getElapsedTime(), 3) ?>s</span>
            </div>
        </div>
    </div>
</footer>

<script>
    // Initialize Lucide icons after footer loads
    lucide.createIcons();
</script>