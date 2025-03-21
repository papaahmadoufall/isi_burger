<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast/use-toast';
import { Toaster } from '@/components/ui/toast';

const { toast } = useToast();

interface Burger {
    id: number;
    name: string;
    price: number;
    quantity: number;
}

interface Command {
    id: number;
    user: {
        id: number;
        name: string;
    };
    burgers: Burger[];
    total: string;
    statut: string;
    created_at: string;
}

const commands = ref<Command[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);
const dialogVisible = ref(false);
const selectedCommand = reactive<Command>({
    id: 0,
    user: { id: 0, name: '' },
    burgers: [],
    total: '0',
    statut: '',
    created_at: '',
});

// Fetch commands
const fetchCommands = async () => {
    try {
        const response = await fetch('/api/commands');
        const data = await response.json();
        commands.value = data;
        isLoading.value = false;
    } catch (err) {
        error.value = 'Failed to load commands';
        isLoading.value = false;
    }
};

// Open view dialog
const openViewDialog = (command: Command) => {
    dialogVisible.value = true;
    Object.assign(selectedCommand, command);
};

// Delete command
const confirmAndDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this command?')) return;
    
    try {
        await Inertia.delete(`/commands/${id}`);
        toast({
            title: "Success",
            description: "Command deleted successfully",
        });
        await fetchCommands();
    } catch (error) {
        toast({
            title: "Error",
            description: "Failed to delete command",
            variant: "destructive",
        });
    }
};

// Load commands on mount
onMounted(() => {
    fetchCommands();
});
</script>

<template>
    <div>
        <Toaster />
        
        <div v-if="isLoading" class="flex justify-center items-center p-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
        
        <div v-else-if="error" class="p-4 text-red-500 bg-red-50 rounded">
            {{ error }}
        </div>
        
        <Table v-else>
            <TableHeader>
                <TableRow>
                    <TableHead>Order ID</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Items</TableHead>
                    <TableHead>Total</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="command in commands" :key="command.id">
                    <TableCell>#{{ command.id }}</TableCell>
                    <TableCell>{{ command.user.name }}</TableCell>
                    <TableCell>
                        <ul class="list-disc list-inside">
                            <li v-for="burger in command.burgers" :key="burger.id">
                                {{ burger.name }} (x{{ burger.quantity }})
                            </li>
                        </ul>
                    </TableCell>
                    <TableCell>${{ command.total }}</TableCell>
                    <TableCell>
                        <span :class="{
                            'px-2 py-1 rounded-full text-xs font-semibold': true,
                            'bg-yellow-100 text-yellow-800': command.statut === 'en attente',
                            'bg-blue-100 text-blue-800': command.statut === 'en préparation',
                            'bg-green-100 text-green-800': command.statut === 'prête',
                            'bg-purple-100 text-purple-800': command.statut === 'payée'
                        }">
                            {{ command.statut }}
                        </span>
                    </TableCell>
                    <TableCell>{{ new Date(command.created_at).toLocaleDateString() }}</TableCell>
                    <TableCell>
                        <div class="flex gap-2">
                            <Button 
                                variant="outline" 
                                size="sm"
                                @click="openViewDialog(command)"
                            >
                                View
                            </Button>
                            <Button 
                                variant="destructive" 
                                size="sm"
                                @click="confirmAndDelete(command.id)"
                            >
                                Delete
                            </Button>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <Dialog v-model:open="dialogVisible">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Command Details</DialogTitle>
                </DialogHeader>
                
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <label class="text-sm font-medium">Order Information</label>
                        <div class="bg-gray-50 text-black p-4 rounded">
                            <p><strong>Order ID:</strong> #{{ selectedCommand.id }}</p>
                            <p><strong>Customer:</strong> {{ selectedCommand.user.name }}</p>
                            <p><strong>Status:</strong> 
                                <span :class="{
                                    'px-2 py-1 rounded-full text-xs font-semibold': true,
                                    'bg-yellow-100 text-yellow-800': selectedCommand.statut === 'en attente',
                                    'bg-blue-100 text-blue-800': selectedCommand.statut === 'en préparation',
                                    'bg-green-100 text-green-800': selectedCommand.statut === 'prête',
                                    'bg-purple-100 text-purple-800': selectedCommand.statut === 'payée'
                                }">
                                    {{ selectedCommand.statut }}
                                </span>
                            </p>
                            <p><strong>Total:</strong> ${{ selectedCommand.total }}</p>
                            <p><strong>Date:</strong> {{ new Date(selectedCommand.created_at).toLocaleDateString() }}</p>
                            <p class="mt-2"><strong>Items:</strong></p>
                            <ul class="list-disc list-inside mt-2">
                                <li v-for="burger in selectedCommand.burgers" :key="burger.id">
                                    {{ burger.name }} (x{{ burger.quantity }})
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <DialogFooter>
                    <Button 
                        variant="outline" 
                        @click="dialogVisible = false"
                    >
                        Close
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template> 