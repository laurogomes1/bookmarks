<?php
// MANTIDO: session_start() é agora gerenciado pelo roteador principal.
// require_once __DIR__ . '/../../layout/common.php';
// require_once __DIR__ . '/../../data.php';
// ALTERADO: Usando as constantes definidas no roteador.
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';


checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// MANTIDO: Sua lógica original para carregar categorias.
$stmt_categories = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY display_order ASC");
$stmt_categories->execute([$user['id']]);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// MANTIDO: Sua lógica original para carregar bookmarks.
$stmt_bookmarks = $pdo->prepare("
    SELECT b.*, c.name as category_name 
    FROM bookmarks b
    JOIN categories c ON b.category_id = c.id
    WHERE b.user_id = ? 
    ORDER BY c.display_order ASC, b.display_order ASC
");
$stmt_bookmarks->execute([$user['id']]);
$bookmarks_by_category = [];
while ($bookmark = $stmt_bookmarks->fetch(PDO::FETCH_ASSOC)) {
    $bookmarks_by_category[$bookmark['category_name']][] = $bookmark;
}

// MANTIDO: Sua estrutura HTML original.
$dashboardContent = '
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Seus Bookmarks</h1>
    <div class="flex items-center gap-4">
        <div id="order-actions" class="hidden flex items-center gap-2">
            <button id="undo-order" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">Desfazer</button>
            <button id="save-order" class="bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition">Salvar Ordem</button>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <a href="/bookmarks/categories/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition flex-grow sm:flex-grow-0">+ Nova Categoria</a>
            <a href="/bookmarks/bookmarks/add" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition flex-grow sm:flex-grow-0">+ Adicionar Bookmark</a>
        </div>
    </div>
</div>
<div id="category-list">';

if (empty($categories)) {
    // MANTIDO: Sua mensagem para quando não há categorias.
    $dashboardContent .= '<div class="text-center bg-white p-10 rounded-lg shadow">
        <p class="text-gray-600">Você ainda não tem categorias.</p>
        <p class="text-gray-500 text-sm mt-2">Comece adicionando uma para organizar seus links.</p>
    </div>';
} else {
    // MANTIDO: Sua lógica para iterar e exibir as categorias.
    foreach ($categories as $category) {
        $dashboardContent .= '<div class="category-container mb-8 p-4 bg-gray-50 rounded-lg" data-id="' . $category['id'] . '">';
        
        $dashboardContent .= '
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-2xl font-semibold text-gray-700 flex items-center cursor-move" title="Arraste para reordenar as categorias">
                <i class="fas fa-grip-vertical text-gray-400 mr-3"></i>
                <i class="fas ' . htmlspecialchars($category['icon']) . ' text-gray-500 mr-3"></i> 
                ' . htmlspecialchars($category['name']) . '
            </h2>
            <div class="flex items-center gap-3">
                <a href="/bookmarks/categories/edit?id=' . $category['id'] . '" class="text-blue-500 hover:text-blue-700" title="Editar Categoria"><i class="fas fa-pencil-alt"></i></a>
                <a href="/bookmarks/categories/delete?id=' . $category['id'] . '" class="text-red-500 hover:text-red-700" title="Excluir Categoria" onclick="return confirm(\'Atenção! Excluir uma categoria também removerá todos os bookmarks dentro dela. Deseja continuar?\');"><i class="fas fa-trash-alt"></i></a>
            </div>
        </div>';
        
        $dashboardContent .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bookmark-grid" id="bookmark-list-' . $category['id'] . '" data-category-id="' . $category['id'] . '">';

        if (!empty($bookmarks_by_category[$category['name']])) {
            // MANTIDO: Sua lógica para iterar e exibir os bookmarks.
            foreach ($bookmarks_by_category[$category['name']] as $bookmark) {
                $dashboardContent .= '
                <div class="bookmark-card bg-white rounded-lg shadow p-5 border-l-4 border-green-500 hover:shadow-lg transition-shadow flex flex-col cursor-move" data-id="' . $bookmark['id'] . '" title="Arraste para reordenar ou mover para outra categoria">
                    <div class="flex-grow">
                        <a href="' . htmlspecialchars($bookmark['url']) . '" target="_blank" class="block"><h3 class="text-xl font-bold text-gray-800 truncate">' . htmlspecialchars($bookmark['title']) . '</h3></a>
                        <p class="text-green-600 hover:underline text-sm truncate my-2"><a href="' . htmlspecialchars($bookmark['url']) . '" target="_blank">' . htmlspecialchars($bookmark['url']) . '</a></p>
                        <p class="text-gray-600 mt-2 text-sm">' . htmlspecialchars($bookmark['description']) . '</p>
                    </div>
                    <div class="mt-4 pt-4 border-t flex justify-end items-center gap-4">
                        <a href="/bookmarks/bookmarks/edit?id=' . $bookmark['id'] . '" class="text-blue-500 hover:text-blue-700" title="Editar Bookmark"><i class="fas fa-pencil-alt"></i></a>
                        <a href="/bookmarks/bookmarks/delete?id=' . $bookmark['id'] . '" class="text-red-500 hover:text-red-700" title="Excluir Bookmark" onclick="return confirm(\'Tem certeza que deseja excluir este bookmark?\');"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </div>';
            }
        }
        $dashboardContent .= '</div>'; // Fim de bookmark-grid
        $dashboardContent .= '</div>'; // Fim de category-container
    }
}
$dashboardContent .= '</div>'; // Fim de category-list

// MANTIDO: Seu bloco de JavaScript original.
$dashboardContent .= "
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderActions = document.getElementById('order-actions');
    const saveButton = document.getElementById('save-order');
    const undoButton = document.getElementById('undo-order');

    function showActionButtons() {
        orderActions.classList.remove('hidden');
    }

    const categoryList = document.getElementById('category-list');
    new Sortable(categoryList, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: showActionButtons
    });

    document.querySelectorAll('.bookmark-grid').forEach(grid => {
        new Sortable(grid, {
            group: 'bookmarks',
            animation: 150,
            handle: '.bookmark-card',
            onEnd: showActionButtons
        });
    });

    undoButton.addEventListener('click', () => location.reload());

    saveButton.addEventListener('click', function() {
        saveButton.disabled = true;
        saveButton.textContent = 'Salvando...';

        const formData = new FormData();

        document.querySelectorAll('#category-list .category-container').forEach(el => {
            formData.append('categories[]', el.dataset.id);
        });

        const bookmarkOrder = {};
        document.querySelectorAll('.bookmark-grid').forEach(grid => {
            const categoryId = grid.dataset.categoryId;
            const bookmarkElements = grid.querySelectorAll('.bookmark-card');
            bookmarkOrder[categoryId] = Array.from(bookmarkElements).map(el => el.dataset.id);
        });
        formData.append('bookmarks', JSON.stringify(bookmarkOrder));

        // ALTERADO: Apenas a URL do fetch para a nova rota.
        fetch('/bookmarks/ajax/save-order', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error('Erro do servidor: ' + text) });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                orderActions.classList.add('hidden');
                // alert('Ordem salva com sucesso!'); // Descomente para testar
            } else {
                alert('Erro ao salvar a ordem: ' + (data.message || 'Erro desconhecido.'));
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Ocorreu um erro de comunicação. A página será recarregada.');
            location.reload();
        })
        .finally(() => {
            saveButton.disabled = false;
            saveButton.textContent = 'Salvar Ordem';
        });
    });
});
</script>
";

echo renderHeader('Dashboard - Bookmarks');
echo renderPageStructure($user, $dashboardContent);
echo renderFooter();
?>