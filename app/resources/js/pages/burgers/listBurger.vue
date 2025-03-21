<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useFetchBurgers } from '@/composables/useFetchBurgers';
import { Inertia } from '@inertiajs/inertia';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useToast } from '@/components/ui/toast/use-toast';
import { Toaster } from '@/components/ui/toast';

const { toast } = useToast();

interface Burger {
    id: number;
    name: string;
    price: number;
    stock: number;
    description: string;
    image: string;
}

// Gestion réactive des données
const { burgers, isLoading, error, fetchBurgers } = useFetchBurgers();
const dialogVisible = ref(false);
const selectedBurger = reactive<Burger>({
    id: 0,
    name: '',
    price: 0,
    stock: 0,
    description: '',
    image: '',
});
const selectedFile = ref<File | null>(null);
const isSubmitting = ref(false);

// Fonction pour ouvrir le formulaire d'édition
const openEditDialog = (burger: Burger) => {
    dialogVisible.value = true;
    Object.assign(selectedBurger, burger);
    selectedFile.value = null;
};

// Fonction pour soumettre les modifications
const submitEdit = async () => {
    if (isSubmitting.value) return;
    
    isSubmitting.value = true;
    const formData = new FormData();
    formData.append('_method', 'PUT'); // Laravel method spoofing
    formData.append('id', selectedBurger.id?.toString() ?? '');
    formData.append('name', selectedBurger.name);
    formData.append('price', selectedBurger.price.toString());
    formData.append('stock', selectedBurger.stock.toString());
    formData.append('description', selectedBurger.description);

    if (selectedFile.value) {
        formData.append('image', selectedFile.value);
    }

    try {
        await Inertia.post(`/burgers/${selectedBurger.id}`, formData);
        toast({
            title: "Success",
            description: "Burger updated successfully",
        });
        dialogVisible.value = false;
        await fetchBurgers();
    } catch (error) {
        toast({
            title: "Error",
            description: "Failed to update burger",
            variant: "destructive",
        });
    } finally {
        isSubmitting.value = false;
    }
};

// Fonction pour confirmer et supprimer un burger
const confirmAndDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this burger?')) return;
    
    try {
        await Inertia.delete(`/burgers/${id}`);
        toast({
            title: "Success",
            description: "Burger deleted successfully",
        });
        await fetchBurgers();
    } catch (error) {
        toast({
            title: "Error",
            description: "Failed to delete burger",
            variant: "destructive",
        });
    }
};

// Charger la liste des burgers au montage du composant
onMounted(() => {
    fetchBurgers();
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
                    <TableHead>ID</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Price</TableHead>
                    <TableHead>Stock</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead>Image</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="burger in burgers" :key="burger.id">
                    <TableCell>{{ burger.id }}</TableCell>
                    <TableCell>{{ burger.name }}</TableCell>
                    <TableCell>${{ burger.price }}</TableCell>
                    <TableCell>{{ burger.stock }}</TableCell>
                    <TableCell>{{ burger.description }}</TableCell>
                    <TableCell>
                        <img 
                            :src="burger.image" 
                            :alt="burger.name" 
                            class="h-12 w-12 object-cover rounded"
                        />
                    </TableCell>
                    <TableCell>
                        <div class="flex gap-2">
                            <Button 
                                variant="outline" 
                                size="sm"
                                @click="openEditDialog(burger)"
                            >
                                Edit
                            </Button>
                            <Button 
                                variant="destructive" 
                                size="sm"
                                @click="confirmAndDelete(burger.id)"
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
                    <DialogTitle>Edit Burger</DialogTitle>
                </DialogHeader>
                
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Name</label>
                        <Input 
                            id="name" 
                            v-model="selectedBurger.name" 
                            type="text" 
                            required
                        />
                    </div>
                    
                    <div class="grid gap-2">
                        <label for="price" class="text-sm font-medium">Price</label>
                        <Input 
                            id="price" 
                            v-model="selectedBurger.price" 
                            type="number" 
                            step="0.01"
                            required
                        />
                    </div>
                    
                    <div class="grid gap-2">
                        <label for="stock" class="text-sm font-medium">Stock</label>
                        <Input 
                            id="stock" 
                            v-model="selectedBurger.stock" 
                            type="number" 
                            required
                        />
                    </div>
                    
                    <div class="grid gap-2">
                        <label for="description" class="text-sm font-medium">Description</label>
                        <Input 
                            id="description" 
                            v-model="selectedBurger.description" 
                            type="text"
                        />
                    </div>
                    
                    <div class="grid gap-2">
                        <label for="image" class="text-sm font-medium">Image</label>
                        <Input
                            id="image"
                            type="file"
                            accept="image/*"
                            @change="(e: Event) => selectedFile = (e.target as HTMLInputElement).files?.[0] || null"
                            class="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90"
                        />
                        <div v-if="selectedBurger.image" class="mt-2">
                            <img 
                                :src="selectedBurger.image" 
                                :alt="selectedBurger.name" 
                                class="h-20 w-20 object-cover rounded"
                            />
                        </div>
                    </div>
                </div>
                
                <DialogFooter>
                    <Button 
                        variant="outline" 
                        @click="dialogVisible = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        @click="submitEdit"
                        :disabled="isSubmitting || !selectedBurger.name || !selectedBurger.price || !selectedBurger.stock"
                    >
                        {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
