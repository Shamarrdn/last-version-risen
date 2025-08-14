@extends('layouts.superadmin')

@section('title', 'الألوان والمقاسات')

@section('page_title', 'الألوان والمقاسات')

@section('content')
<div class="container-fluid">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="sizesColorsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sizes-tab" data-bs-toggle="tab" data-bs-target="#sizes" type="button" role="tab" aria-controls="sizes" aria-selected="true">
                <i class="fas fa-ruler me-2"></i>المقاسات
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab" aria-controls="colors" aria-selected="false">
                <i class="fas fa-palette me-2"></i>الألوان
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="sizesColorsTabsContent">
        <!-- Sizes Tab -->
        <div class="tab-pane fade show active" id="sizes" role="tabpanel" aria-labelledby="sizes-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة المقاسات</h5>
                    <button type="button" class="btn btn-primary" id="addSizeBtn">
                        <i class="fas fa-plus me-2"></i>إضافة مقاس جديد
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($sizes as $size)
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="size-card border rounded p-3 text-center">
                                <div class="size-name h5 mb-2">{{ $size->name }}</div>
                                @if($size->description)
                                <div class="size-description small text-muted mb-2">{{ $size->description }}</div>
                                @endif
                                <div class="size-actions">
                                    <button class="btn btn-sm btn-outline-primary me-2 edit-size-btn" data-id="{{ $size->id }}" data-name="{{ $size->name }}" data-description="{{ $size->description }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-size-btn" data-id="{{ $size->id }}" data-name="{{ $size->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-ruler fa-3x mb-3"></i>
                                <h5>لا توجد مقاسات متاحة</h5>
                                <p>قم بإضافة مقاسات جديدة للبدء</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Colors Tab -->
        <div class="tab-pane fade" id="colors" role="tabpanel" aria-labelledby="colors-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة الألوان</h5>
                    <button type="button" class="btn btn-primary" id="addColorBtn">
                        <i class="fas fa-plus me-2"></i>إضافة لون جديد
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($colors as $color)
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="color-card border rounded p-3 text-center">
                                <div class="color-preview mb-2" style="width: 50px; height: 50px; background-color: {{ $color->code }}; border-radius: 50%; margin: 0 auto; border: 2px solid #ddd;"></div>
                                <div class="color-name h6 mb-2">{{ $color->name }}</div>
                                <div class="color-code small text-muted mb-2">{{ $color->code }}</div>
                                @if($color->description)
                                <div class="color-description small text-muted mb-2">{{ $color->description }}</div>
                                @endif
                                <div class="color-actions">
                                    <button class="btn btn-sm btn-outline-primary me-2 edit-color-btn" data-id="{{ $color->id }}" data-name="{{ $color->name }}" data-code="{{ $color->code }}" data-description="{{ $color->description }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-color-btn" data-id="{{ $color->id }}" data-name="{{ $color->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-palette fa-3x mb-3"></i>
                                <h5>لا توجد ألوان متاحة</h5>
                                <p>قم بإضافة ألوان جديدة للبدء</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Size Modal -->
<div class="modal" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">إضافة مقاس جديد</h5>
                <button type="button" class="close-modal" aria-label="Close">&times;</button>
            </div>
            <form id="addSizeForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sizeName" class="form-label">اسم المقاس</label>
                        <input type="text" class="form-control" id="sizeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="sizeDescription" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="sizeDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Color Modal -->
<div class="modal" id="addColorModal" tabindex="-1" aria-labelledby="addColorModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">إضافة لون جديد</h5>
                <button type="button" class="close-modal" aria-label="Close">&times;</button>
            </div>
            <form id="addColorForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="colorName" class="form-label">اسم اللون</label>
                        <input type="text" class="form-control" id="colorName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="colorCode" class="form-label">كود اللون</label>
                        <input type="color" class="form-control form-control-color" id="colorCode" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="colorDescription" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="colorDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Size Modal -->
<div class="modal" id="editSizeModal" tabindex="-1" aria-labelledby="editSizeModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSizeModalLabel">تعديل المقاس</h5>
                <button type="button" class="close-modal" aria-label="Close">&times;</button>
            </div>
            <form id="editSizeForm">
                @csrf
                <input type="hidden" id="editSizeId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editSizeName" class="form-label">اسم المقاس</label>
                        <input type="text" class="form-control" id="editSizeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSizeDescription" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="editSizeDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Color Modal -->
<div class="modal" id="editColorModal" tabindex="-1" aria-labelledby="editColorModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editColorModalLabel">تعديل اللون</h5>
                <button type="button" class="close-modal" aria-label="Close">&times;</button>
            </div>
            <form id="editColorForm">
                @csrf
                <input type="hidden" id="editColorId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editColorName" class="form-label">اسم اللون</label>
                        <input type="text" class="form-control" id="editColorName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editColorCode" class="form-label">كود اللون</label>
                        <input type="color" class="form-control form-control-color" id="editColorCode" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="editColorDescription" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="editColorDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom modal styling without Bootstrap */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal.show {
    display: block;
}

.modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 500px;
}

.modal-content {
    position: relative;
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    outline: 0;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 500;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    opacity: 0.5;
    cursor: pointer;
    padding: 0;
    margin: 0;
}

.close-modal:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    gap: 0.5rem;
}
</style>

<script>
// Simple modal functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add Size Button
    document.getElementById('addSizeBtn').addEventListener('click', function() {
        showModal('addSizeModal');
    });

    // Add Color Button
    document.getElementById('addColorBtn').addEventListener('click', function() {
        showModal('addColorModal');
    });

    // Edit Size Buttons
    document.querySelectorAll('.edit-size-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            
            document.getElementById('editSizeId').value = id;
            document.getElementById('editSizeName').value = name;
            document.getElementById('editSizeDescription').value = description || '';
            
            showModal('editSizeModal');
        });
    });

    // Edit Color Buttons
    document.querySelectorAll('.edit-color-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const code = this.getAttribute('data-code');
            const description = this.getAttribute('data-description');
            
            document.getElementById('editColorId').value = id;
            document.getElementById('editColorName').value = name;
            document.getElementById('editColorCode').value = code;
            document.getElementById('editColorDescription').value = description || '';
            
            showModal('editColorModal');
        });
    });

    // Delete Size Buttons
    document.querySelectorAll('.delete-size-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            if (confirm('هل أنت متأكد من حذف المقاس ' + name + '؟')) {
                fetch(`/superadmin/sizes-colors/sizes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء حذف المقاس');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء حذف المقاس');
                });
            }
        });
    });

    // Delete Color Buttons
    document.querySelectorAll('.delete-color-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            if (confirm('هل أنت متأكد من حذف اللون ' + name + '؟')) {
                fetch(`/superadmin/sizes-colors/colors/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء حذف اللون');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء حذف اللون');
                });
            }
        });
    });

    // Close modal buttons
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                hideModal(modal.id);
            }
        });
    });
    
    // Close modal on backdrop click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal(this.id);
            }
        });
    });

    // Form submissions
    document.getElementById('addSizeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const sizeName = document.getElementById('sizeName').value.trim();
        if (!sizeName) {
            alert('يرجى إدخال اسم المقاس');
            return;
        }
        
        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/superadmin/sizes-colors/sizes', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                let errorMessage = data.message || 'حدث خطأ أثناء إضافة المقاس';
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nالتفاصيل:\n' + errorDetails;
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إضافة المقاس');
        });
    });

    document.getElementById('addColorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const colorName = document.getElementById('colorName').value.trim();
        const colorCode = document.getElementById('colorCode').value;
        
        if (!colorName) {
            alert('يرجى إدخال اسم اللون');
            return;
        }
        
        if (!colorCode) {
            alert('يرجى اختيار كود اللون');
            return;
        }
        
        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/superadmin/sizes-colors/colors', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                let errorMessage = data.message || 'حدث خطأ أثناء إضافة اللون';
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nالتفاصيل:\n' + errorDetails;
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إضافة اللون');
        });
    });

    document.getElementById('editSizeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const sizeName = document.getElementById('editSizeName').value.trim();
        if (!sizeName) {
            alert('يرجى إدخال اسم المقاس');
            return;
        }
        
        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        const id = document.getElementById('editSizeId').value;
        
        fetch(`/superadmin/sizes-colors/sizes/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                let errorMessage = data.message || 'حدث خطأ أثناء تحديث المقاس';
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nالتفاصيل:\n' + errorDetails;
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحديث المقاس');
        });
    });

    document.getElementById('editColorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const colorName = document.getElementById('editColorName').value.trim();
        const colorCode = document.getElementById('editColorCode').value;
        
        if (!colorName) {
            alert('يرجى إدخال اسم اللون');
            return;
        }
        
        if (!colorCode) {
            alert('يرجى اختيار كود اللون');
            return;
        }
        
        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        const id = document.getElementById('editColorId').value;
        
        fetch(`/superadmin/sizes-colors/colors/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                let errorMessage = data.message || 'حدث خطأ أثناء تحديث اللون';
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nالتفاصيل:\n' + errorDetails;
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحديث اللون');
        });
    });
});
</script>

@endsection
