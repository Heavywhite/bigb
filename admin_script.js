// admin_script.js

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('adminSidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const mobileToggle = document.getElementById('mobileToggle');
  const productModal = document.getElementById('productModal');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const addProductBtn = document.getElementById('addProductBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const productForm = document.getElementById('productForm');
  const imageInput = document.getElementById('productImages');
  const imagePreview = document.getElementById('imagePreview');
  const productsTableBody = document.getElementById('productsTableBody');
  const selectAllCheckbox = document.getElementById('selectAll');
  const productSearch = document.getElementById('productSearch');
  const categoryFilter = document.getElementById('categoryFilter');
  const stockFilter = document.getElementById('stockFilter');
  const statusFilter = document.getElementById('statusFilter');
  const exportBtn = document.getElementById('exportBtn');

  // Sample product data
  let products = [
    {
      id: 1,
      thumbnail: '📱',
      name: 'Samsung Galaxy A54',
      meta: '128GB, 6GB RAM',
      sku: 'SAM-A54-128',
      category: 'electronics',
      price: 850000,
      stock: 15,
      lowStockThreshold: 5,
      status: 'active',
      featured: false,
      description: '',
      brand: '',
      images: [],
    },
    {
      id: 2,
      thumbnail: '💻',
      name: 'MacBook Pro 13"',
      meta: 'M2, 8GB RAM, 256GB SSD',
      sku: 'APL-MBP13-M2',
      category: 'electronics',
      price: 4500000,
      stock: 5,
      lowStockThreshold: 5,
      status: 'active',
      featured: false,
      description: '',
      brand: '',
      images: [],
    },
    {
      id: 3,
      thumbnail: '👟',
      name: 'Nike Air Max Sneakers',
      meta: 'Multiple sizes available',
      sku: 'NKE-AM-001',
      category: 'fashion',
      price: 320000,
      stock: 25,
      lowStockThreshold: 5,
      status: 'active',
      featured: false,
      description: '',
      brand: '',
      images: [],
    },
    {
      id: 4,
      thumbnail: '🛋️',
      name: 'Leather Sofa Set',
      meta: '3-seater, Genuine leather',
      sku: 'HOME-SOFA-001',
      category: 'home',
      price: 1200000,
      stock: 3,
      lowStockThreshold: 5,
      status: 'active',
      featured: false,
      description: '',
      brand: '',
      images: [],
    },
    {
      id: 5,
      thumbnail: '🧴',
      name: 'Skincare Set Premium',
      meta: 'Complete routine set',
      sku: 'BEAUTY-SKIN-001',
      category: 'beauty',
      price: 150000,
      stock: 0,
      lowStockThreshold: 5,
      status: 'inactive',
      featured: false,
      description: '',
      brand: '',
      images: [],
    },
  ];

  let filteredProducts = [...products];

  // Sidebar toggle (desktop and mobile)
  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });
  if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });
  }

  // Show modal
  function showAddProductModal() {
    productForm.reset();
    imagePreview.innerHTML = '';
    productModal.hidden = false;
    productModal.querySelector('input, select, textarea').focus();
  }

  // Close modal
  function closeProductModal() {
    productModal.hidden = true;
  }

  addProductBtn.addEventListener('click', showAddProductModal);
  modalCloseBtn.addEventListener('click', closeProductModal);
  cancelBtn.addEventListener('click', closeProductModal);

  // Close modal on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !productModal.hidden) {
      closeProductModal();
    }
  });

  // Image preview
  imageInput.addEventListener('change', () => {
    imagePreview.innerHTML = '';
    const files = imageInput.files;
    if (files.length === 0) return;

    Array.from(files).forEach((file) => {
      if (!file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = file.name;
        img.style.maxWidth = '80px';
        img.style.maxHeight = '80px';
        img.style.marginRight = '8px';
        img.style.borderRadius = '8px';
        imagePreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });

  // Render products table rows
  function renderProductsTable(productsList) {
    productsTableBody.innerHTML = '';
    if (productsList.length === 0) {
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.colSpan = 8;
      td.textContent = 'No products found.';
      td.style.textAlign = 'center';
      tr.appendChild(td);
      productsTableBody.appendChild(tr);
      return;
    }

    productsList.forEach((product) => {
      const tr = document.createElement('tr');

      // Checkbox
      const tdCheckbox = document.createElement('td');
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.classList.add('product-checkbox');
      checkbox.dataset.id = product.id;
      tdCheckbox.appendChild(checkbox);
      tr.appendChild(tdCheckbox);

      // Product cell
      const tdProduct = document.createElement('td');
      tdProduct.classList.add('product-cell');
      const thumbDiv = document.createElement('div');
      thumbDiv.classList.add('product-thumbnail');
      thumbDiv.textContent = product.thumbnail;
      const infoDiv = document.createElement('div');
      const nameDiv = document.createElement('div');
      nameDiv.classList.add('product-name');
      nameDiv.textContent = product.name;
      const metaDiv = document.createElement('div');
      metaDiv.classList.add('product-meta');
      metaDiv.textContent = product.meta;
      infoDiv.appendChild(nameDiv);
      infoDiv.appendChild(metaDiv);
      tdProduct.appendChild(thumbDiv);
      tdProduct.appendChild(infoDiv);
      tr.appendChild(tdProduct);

      // SKU
      const tdSKU = document.createElement('td');
      tdSKU.textContent = product.sku;
      tr.appendChild(tdSKU);

      // Category
      const tdCategory = document.createElement('td');
      tdCategory.textContent = capitalizeFirstLetter(product.category);
      tr.appendChild(tdCategory);

      // Price
      const tdPrice = document.createElement('td');
      tdPrice.textContent = `UGX ${product.price.toLocaleString()}`;
      tr.appendChild(tdPrice);

      // Stock
      const tdStock = document.createElement('td');
      const stockSpan = document.createElement('span');
      stockSpan.classList.add('stock-badge');
      if (product.stock === 0) {
        stockSpan.classList.add('out-of-stock');
      } else if (product.stock <= product.lowStockThreshold) {
        stockSpan.classList.add('low-stock');
      } else {
        stockSpan.classList.add('in-stock');
      }
      stockSpan.textContent = product.stock;
      tdStock.appendChild(stockSpan);
      tr.appendChild(tdStock);

      // Status
      const tdStatus = document.createElement('td');
      const statusSpan = document.createElement('span');
      statusSpan.classList.add('status-badge');
      statusSpan.classList.add(product.status === 'active' ? 'active' : 'inactive');
      statusSpan.textContent = capitalizeFirstLetter(product.status);
      tdStatus.appendChild(statusSpan);
      tr.appendChild(tdStatus);

      // Actions
      const tdActions = document.createElement('td');
      const actionDiv = document.createElement('div');
      actionDiv.classList.add('action-buttons');

      // Edit button
      const editBtn = document.createElement('button');
      editBtn.classList.add('icon-btn');
      editBtn.title = 'Edit';
      editBtn.innerHTML = '✏️';
      editBtn.addEventListener('click', () => {
        openEditProductModal(product.id);
      });
      actionDiv.appendChild(editBtn);

      // View button
      const viewBtn = document.createElement('button');
      viewBtn.classList.add('icon-btn');
      viewBtn.title = 'View';
      viewBtn.innerHTML = '👁️';
      viewBtn.addEventListener('click', () => {
        alert(`Viewing product: ${product.name}`);
      });
      actionDiv.appendChild(viewBtn);

      // Delete button
      const deleteBtn = document.createElement('button');
      deleteBtn.classList.add('icon-btn', 'delete');
      deleteBtn.title = 'Delete';
      deleteBtn.innerHTML = '🗑️';
      deleteBtn.addEventListener('click', () => {
        if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
          deleteProduct(product.id);
        }
      });
      actionDiv.appendChild(deleteBtn);

      tdActions.appendChild(actionDiv);
      tr.appendChild(tdActions);

      productsTableBody.appendChild(tr);
    });
  }

  // Capitalize first letter helper
  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  // Delete product by id
  function deleteProduct(id) {
    products = products.filter((p) => p.id !== id);
    applyFilters();
  }

  // Apply filters and search
  function applyFilters() {
    const searchTerm = productSearch.value.trim().toLowerCase();
    const category = categoryFilter.value;
    const stockStatus = stockFilter.value;
    const status = statusFilter.value;

    filteredProducts = products.filter((product) => {
      // Search filter
      const matchesSearch =
        product.name.toLowerCase().includes(searchTerm) ||
        product.sku.toLowerCase().includes(searchTerm) ||
        product.category.toLowerCase().includes(searchTerm);

      // Category filter
      const matchesCategory = category ? product.category === category : true;

      // Stock filter
      let matchesStock = true;
      if (stockStatus === 'in_stock') matchesStock = product.stock > product.lowStockThreshold;
      else if (stockStatus === 'low_stock') matchesStock = product.stock > 0 && product.stock <= product.lowStockThreshold;
      else if (stockStatus === 'out_of_stock') matchesStock = product.stock === 0;

      // Status filter
      const matchesStatus = status ? product.status === status : true;

      return matchesSearch && matchesCategory && matchesStock && matchesStatus;
    });

    renderProductsTable(filteredProducts);
    updateStats();
  }

  // Update stats numbers
  function updateStats() {
    const total = products.length;
    const active = products.filter((p) => p.status === 'active').length;
    const lowStock = products.filter((p) => p.stock > 0 && p.stock <= p.lowStockThreshold).length;
    const outOfStock = products.filter((p) => p.stock === 0).length;

    document.getElementById('totalProducts').textContent = total;
    document.getElementById('activeProducts').textContent = active;
    document.getElementById('lowStockProducts').textContent = lowStock;
    document.getElementById('outOfStockProducts').textContent = outOfStock;
  }

  // Select all checkbox functionality
  selectAllCheckbox.addEventListener('change', () => {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach((cb) => {
      cb.checked = selectAllCheckbox.checked;
    });
  });

  // Search input event
  productSearch.addEventListener('input', () => {
    applyFilters();
  });

  // Filters change event
  [categoryFilter, stockFilter, statusFilter].forEach((filter) => {
    filter.addEventListener('change', () => {
      applyFilters();
    });
  });

  // Export products as CSV
  exportBtn.addEventListener('click', () => {
    exportProductsCSV(filteredProducts);
  });

  function exportProductsCSV(productsToExport) {
    if (productsToExport.length === 0) {
      alert('No products to export.');
      return;
    }

    const headers = ['ID', 'Name', 'SKU', 'Category', 'Price (UGX)', 'Stock', 'Status', 'Featured'];
    const rows = productsToExport.map((p) => [
      p.id,
      `"${p.name.replace(/"/g, '""')}"`,
      p.sku,
      p.category,
      p.price,
      p.stock,
      p.status,
      p.featured ? 'Yes' : 'No',
    ]);

    let csvContent = headers.join(',') + '\n';
    rows.forEach((row) => {
      csvContent += row.join(',') + '\n';
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'products_export.csv';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  // Open edit modal (for demo, just alert)
  function openEditProductModal(productId) {
    const product = products.find((p) => p.id === productId);
    if (!product) return;

    // For demo, just alert product name
    alert(`Edit product: ${product.name}`);

    // In real app, populate modal fields and show modal
  }

  // Handle form submission (add new product)
  productForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Simple validation
    if (!productForm.checkValidity()) {
      productForm.reportValidity();
      return;
    }

    // Gather form data
    const formData = new FormData(productForm);
    const newProduct = {
      id: products.length ? Math.max(...products.map((p) => p.id)) + 1 : 1,
      thumbnail: '🆕', // Default icon for new product
      name: formData.get('name').trim(),
      meta: '', // Could be built from other fields
      sku: formData.get('sku').trim(),
      category: formData.get('category'),
      price: Number(formData.get('price')),
      stock: Number(formData.get('stock')),
      lowStockThreshold: Number(formData.get('low_stock')) || 5,
      status: formData.get('active') === 'on' || formData.get('active') === 'true' ? 'active' : 'inactive',
      featured: formData.get('featured') === 'on' || formData.get('featured') === 'true',
      description: formData.get('description').trim(),
      brand: formData.get('brand').trim(),
      images: [], // Image upload handling can be added here
    };

    products.push(newProduct);
    applyFilters();
    closeProductModal();
    alert(`Product "${newProduct.name}" added successfully.`);
  });

  // Initial render
  applyFilters();
  updateStats();

  // Dummy logout function
  window.adminLogout = function () {
    if (confirm('Are you sure you want to logout?')) {
      // Redirect to login or homepage
      window.location.href = 'index.php';
    }
  };
});