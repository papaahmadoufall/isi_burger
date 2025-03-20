<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useFetchBurgers } from '@/composables/useFetchBurgers';
import { Inertia } from '@inertiajs/inertia';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'; // Exemple avec ShadCDN
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'; // Imports corrects
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';

// Gestion réactive des données
const { burgers, isLoading, error, fetchBurgers } = useFetchBurgers();
const dialogVisible = ref(false); // Contrôle l'état d'affichage du dialogue
const selectedBurger = reactive({
    id: null, // ID du burger sélectionné
    name: '',
    price: 0,
    stock: 0,
    description: '',
    image: '', // Préserve l'URL existante
});
const selectedFile = ref<File | null>(null); // Réactif pour gérer le fichier sélectionné

// Fonction pour ouvrir le formulaire d'édition
const openEditDialog = (burger: Record<string, any>) => {
    dialogVisible.value = true; // Change l'état du dialogue à "ouvert"
    Object.assign(selectedBurger, burger); // Copie les données du burger sélectionné dans selectedBurger
    selectedFile.value = null; // Réinitialiser l'entrée du fichier
};

// Fonction pour soumettre les modifications
const submitEdit = () => {
    const formData = new FormData();
    formData.append('id', selectedBurger.id?.toString() ?? '');
    formData.append('name', selectedBurger.name);
    formData.append('price', selectedBurger.price.toString());
    formData.append('stock', selectedBurger.stock.toString());
    formData.append('description', selectedBurger.description);

    if (selectedFile.value) {
        formData.append('image', selectedFile.value); // Inclure l'image si elle est sélectionnée
    }

    Inertia.post(`/burgers/${selectedBurger.id}`, formData, {
        onSuccess: () => {
            toast({
                title: 'Succès',
                description: 'Burger mis à jour avec succès !',
                variant: 'success',
            });
            dialogVisible.value = false; // Fermer le dialogue
            fetchBurgers(); // Rafraîchit les données
        },
        onError: () => {
            toast({
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la mise à jour.',
                variant: 'destructive',
            });
        },
    });
};

// Fonction pour confirmer et supprimer un burger
const confirmAndDelete = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce burger ?')) {
        Inertia.delete(`/burgers/${id}`, {
            onSuccess: () => {
                toast({
                    title: 'Succès',
                    description: 'Burger supprimé avec succès.',
                    variant: 'success',
                });
                fetchBurgers(); // Rafraîchit les données
            },
            onError: () => {
                toast({
                    title: 'Erreur',
                    description: 'Impossible de supprimer le burger.',
                    variant: 'destructive',
                });
            },
        });
    }
};

// Charger la liste des burgers au montage du composant
onMounted(() => {
    fetchBurgers();
});
</script>

<template>
    <div v-if="isLoading">Chargement...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <Table v-else>
        <TableHeader>
            <TableRow>
                <TableHead>ID</TableHead>
                <TableHead>Nom</TableHead>
                <TableHead>Prix</TableHead>
                <TableHead>Stock</TableHead>
                <TableHead>Description</TableHead>
                <TableHead>Image</TableHead>
                <TableHead>Action</TableHead>
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
                    <img :src="burger.image" alt="Burger Image" class="h-12 w-12 object-cover" />
                </TableCell>
                <TableCell>
                    <button class="mr-4 text-blue-500 hover:underline" @click="openEditDialog(burger)">Edit</button>
                    <button class="text-red-500 hover:underline" @click="confirmAndDelete(burger.id)">Delete</button>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>

    <!-- Dialog pour l'édition d'un burger -->
    <Dialog v-model="dialogVisible">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Modifier le Burger</DialogTitle>
            </DialogHeader>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <Input id="name" v-model="selectedBurger.name" type="text" required />
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Prix</label>
                    <Input id="price" v-model="selectedBurger.price" type="number" required />
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <Input id="stock" v-model="selectedBurger.stock" type="number" required />
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <Input id="description" v-model="selectedBurger.description" type="text" />
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image (Fichier)</label>
                    <input
                        id="image"
                        type="file"
                        accept="image/*"
                        @change="
                            (e) => {
                                return (selectedFile.value = e.target.files?.[0] || null);
                            }
                        "
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                </div>
            </div>
            <DialogFooter class="mt-6">
                <Button variant="outline" @click="dialogVisible = false">Annuler</Button>
                <Button @click="submitEdit" :disabled="!selectedBurger.name || !selectedBurger.price || !selectedBurger.stock"> Enregistrer </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
