<div class="ml-auto w-full sm:col-span-3 col-span-4">
    <form id="frmBuscador" action=""  onsubmit="return buscarProducto();">
        <label for="default-search" class="mb-2 text-sm font-medium text-[#97847d] sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-[#97847d] aria-hidden=" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" name="q" id="txtBuscador" class="block w-full py-3 pl-10 ps-10 text-sm text-[#ae978f] border border-gray-300 rounded-lg bg-gray-50 focus-visible:border-[#97847d]" placeholder="Buscar" required />
            <button type="submit" class="text-white absolute end-2.5 bottom-1 top-1 bg-[#97847d] hover:bg-[#ae978f] focus:ring-4 focus:outline-none focus:ring-[#ae978f] font-medium rounded-lg text-sm px-4 py-2">Buscar</button>
        </div>
    </form>
</div>

<script>
function buscarProducto() {
    var form = document.getElementById('frmBuscador');
    var query = document.getElementById('txtBuscador').value;
    query = query.replace(/\s+/g, '+');
    form.action = '<?php echo BASE_URL_STORE ?>buscar/pagina/1?q=' + encodeURIComponent(query);
    return true;
}
</script>