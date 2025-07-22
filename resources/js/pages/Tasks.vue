<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Task } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader as DialogHeaderModal, DialogTitle, DialogFooter as DialogFooterModal } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import Select from '@/components/ui/select/Select.vue';

const page = usePage();
const tasks = ref<Task[]>(Array.isArray(page.props.tasks) ? page.props.tasks : []);
const filterStatus = ref<string>('');
const order = ref<'asc' | 'desc'>('asc');
const showDialog = ref(false);
const editingTask = ref<Task | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tasks', href: '/tasks' },
];

const filteredTasks = computed(() => {
    let filtered = tasks.value;
    if (filterStatus.value) {
        filtered = filtered.filter(t => t.status === filterStatus.value);
    }
    filtered = [...filtered].sort((a, b) => {
        if (order.value === 'asc') {
            return (a.deadline || '').localeCompare(b.deadline || '');
        } else {
            return (b.deadline || '').localeCompare(a.deadline || '');
        }
    });
    return filtered;
});

function openCreate() {
    editingTask.value = null;
    showDialog.value = true;
}

function openEdit(task: Task) {
    editingTask.value = { ...task };
    showDialog.value = true;
}

function closeDialog() {
    showDialog.value = false;
    editingTask.value = null;
}

const form = useForm({
    title: '',
    description: '',
    status: 'pending',
    deadline: '',
});

function submitForm() {
    if (editingTask.value) {
        router.put(`/tasks/${editingTask.value.id}`, form.data(), {
            onSuccess: () => {
                closeDialog();
                router.reload({ only: ['tasks'] });
            },
        });
    } else {
        router.post('/tasks', form.data(), {
            onSuccess: () => {
                closeDialog();
                router.reload({ only: ['tasks'] });
            },
        });
    }
}

function deleteTask(task: Task) {
    if (confirm('Deseja realmente deletar esta tarefa?')) {
        router.delete(`/tasks/${task.id}`, {
            onSuccess: () => router.reload({ only: ['tasks'] }),
        });
    }
}

function setFormForEdit(task: Task) {
    form.title = task.title;
    form.description = task.description;
    form.status = task.status;
    form.deadline = task.deadline || '';
}

function formatDeadline(deadline: string | null | undefined): string {
    if (!deadline) return 'Sem prazo';
    // Tenta parsear como data completa ou apenas data
    const date = deadline.length > 10 ? new Date(deadline) : new Date(deadline + 'T00:00:00');
    if (isNaN(date.getTime())) return 'Sem prazo';
    return date.toLocaleDateString();
}

watch(showDialog, (val) => {
    if (val && editingTask.value) {
        setFormForEdit(editingTask.value);
    } else if (val === false) {
        form.reset();
    }
});
</script>

<template>
    <Head title="Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex flex-wrap gap-2 items-center">
                <Button @click="openCreate">Nova Tarefa</Button>
                <Label>Status:</Label>
                <Select v-model="filterStatus">
                    <option value="">Todos</option>
                    <option value="pending">Pendente</option>
                    <option value="finished">Concluída</option>
                </Select>
                <Label>Ordenar por prazo:</Label>
                <Select v-model="order">
                    <option value="asc">Ascendente</option>
                    <option value="desc">Descendente</option>
                </Select>
            </div>
            <Separator />
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border rounded-xl border text-sm">
                    <thead class="bg-muted">
                        <tr>
                            <th class="px-4 py-2 text-left">Título</th>
                            <th class="px-4 py-2 text-left">Descrição</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Prazo</th>
                            <th class="px-4 py-2 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in filteredTasks" :key="task.id" class="hover:bg-accent/30 transition-colors">
                            <td class="px-4 py-2 font-medium">{{ task.title }}</td>
                            <td class="px-4 py-2">{{ task.description }}</td>
                            <td class="px-4 py-2">{{ task.status === 'finished' ? 'Concluída' : 'Pendente' }}</td>
                            <td class="px-4 py-2">{{ formatDeadline(task.deadline) }}</td>
                            <td class="px-4 py-2">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost">⋮</Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <DropdownMenuItem @click="openEdit(task)">Editar</DropdownMenuItem>
                                        <DropdownMenuItem @click="deleteTask(task)">Deletar</DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr v-if="filteredTasks.length === 0">
                            <td colspan="5" class="text-center py-4 text-muted-foreground">Nenhuma tarefa encontrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Dialog v-model:open="showDialog">
            <DialogContent>
                <DialogHeaderModal>
                    <DialogTitle>{{ editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}</DialogTitle>
                </DialogHeaderModal>
                <form @submit.prevent="submitForm" class="flex flex-col gap-4">
                    <div>
                        <Label for="title">Título</Label>
                        <Input id="title" v-model="form.title" required />
                    </div>
                    <div>
                        <Label for="description">Descrição</Label>
                        <Input id="description" v-model="form.description" required />
                    </div>
                    <div>
                        <Label for="status">Status</Label>
                        <Select id="status" v-model="form.status">
                            <option selected value="pending">Pendente</option>
                            <option value="finished">Concluída</option>
                        </Select>
                    </div>
                    <div>
                        <Label for="deadline">Prazo</Label>
                        <Input id="deadline" type="date" v-model="form.deadline" required />
                    </div>
                    <DialogFooterModal>
                        <Button type="submit">Salvar</Button>
                        <Button type="button" variant="secondary" @click="closeDialog">Cancelar</Button>
                    </DialogFooterModal>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
