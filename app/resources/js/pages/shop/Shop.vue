<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useFetchBurgers } from '@/composables/useFetchBurgers';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';
import { Inertia } from '@inertiajs/inertia';
import { Link, usePage } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';

const page = usePage();
const isAuthenticated = page.props.isAuthenticated;
const userRole = page.props.userRole;

const { burgers, isLoading, error, fetchBurgers } = useFetchBurgers();
const selectedBurger = ref<any>(null);
const quantity = ref(1);

onMounted(() => {
    fetchBurgers();
});

const openDetails = (burger: any) => {
    selectedBurger.value = burger;
    quantity.value = 1;
};

const handleOrder = () => {
    if (!isAuthenticated) {
        // Redirect to login if not authenticated
        window.location.href = route('login');
        return;
    }

    if (!selectedBurger.value) return;

    const formData = new FormData();
    formData.append('burger_id', selectedBurger.value.id.toString());
    formData.append('quantite', quantity.value.toString());

    Inertia.post('/commands', formData, {
        onSuccess: () => {
            toast({
                title: 'Succès',
                description: 'Commande ajoutée avec succès !',
                variant: 'success',
            });
            fetchBurgers(); // Refresh the burgers list to update stock
        },
        onError: (errors) => {
            toast({
                title: 'Erreur',
                description: errors.message || 'Une erreur est survenue lors de la commande.',
                variant: 'destructive',
            });
        },
    });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold">Notre Menu</h1>
            <div class="flex items-center gap-4">
                <template v-if="isAuthenticated">
                    <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-red-500 hover:text-red-600">
                        <LogOut class="h-4 w-4" />
                        Se déconnecter
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="text-primary hover:underline">Se connecter</Link>
                    <Link :href="route('register')" class="rounded-md bg-primary px-4 py-2 text-white hover:bg-primary/90">
                        S'inscrire
                    </Link>
                </template>
            </div>
        </div>
        
        <div v-if="isLoading" class="text-center">Chargement...</div>
        <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="burger in burgers" :key="burger.id" class="overflow-hidden">
                <img :src="burger.image" :alt="burger.name" class="h-48 w-full object-cover" />
                <CardHeader>
                    <CardTitle>{{ burger.name }}</CardTitle>
                    <CardDescription>{{ burger.description }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-lg font-semibold">${{ burger.price }}</p>
                    <p class="text-sm text-gray-500">Stock disponible: {{ burger.stock }}</p>
                </CardContent>
                <CardFooter>
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button @click="openDetails(burger)">Voir les détails</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>{{ burger.name }}</DialogTitle>
                                <DialogDescription>{{ burger.description }}</DialogDescription>
                            </DialogHeader>
                            <div class="mt-4">
                                <img :src="burger.image" :alt="burger.name" class="h-64 w-full rounded-lg object-cover" />
                                <div class="mt-4">
                                    <p class="text-lg font-semibold">${{ burger.price }}</p>
                                    <p class="text-sm text-gray-500">Stock disponible: {{ burger.stock }}</p>
                                </div>
                                <div v-if="isAuthenticated" class="mt-4 flex items-center gap-4">
                                    <label for="quantity" class="text-sm font-medium">Quantité:</label>
                                    <Input
                                        id="quantity"
                                        v-model="quantity"
                                        type="number"
                                        min="1"
                                        :max="burger.stock"
                                        class="w-24"
                                    />
                                </div>
                                <div v-else class="mt-4">
                                    <p class="text-sm text-gray-500">Connectez-vous pour commander</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <Button v-if="isAuthenticated" @click="handleOrder">Commander</Button>
                                <Link
                                    v-else
                                    :href="route('login')"
                                    class="rounded-md bg-primary px-4 py-2 text-white hover:bg-primary/90"
                                >
                                    Se connecter pour commander
                                </Link>
                            </div>
                        </DialogContent>
                    </Dialog>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>

<style scoped>

</style>
