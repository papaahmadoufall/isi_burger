<script setup lang="ts">
// Imports nécessaires
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/burgers/Layout.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Form,
    FormField,
    FormControl,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import ListBurger from "@/pages/burgers/listBurger.vue";
import { h, ref } from "vue";

// Schéma de validation utilisant Zod
const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(3, "Le nom est obligatoire").max(50, "Le nom est trop long"),
        price: z.number().min(0, "Le prix doit être positif"),
        stock: z.number().min(0, "Le stock doit être positif"),
        description: z.string().optional(),
        image: z.any().optional(), // Permet les fichiers
    })
);

// Initialisation des données du formulaire avec InertiaJS
const form = useForm({
    name: '',        // Nom du burger
    price: 0,        // Prix du burger
    stock: 0,        // Stock disponible
    description: '', // Description optionnelle
    image: null,     // Image (fichier) optionnelle
});

const selectedFile = ref<File | null>(null); // Gestion du fichier sélectionné

// Fonction de soumission
const handleSubmit = () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('price', form.price.toString());
    formData.append('stock', form.stock.toString());
    formData.append('description', form.description || '');

    // Ajouter le fichier image s'il est sélectionné
    if (selectedFile.value) {
        formData.append('image', selectedFile.value);
    }

    form.post(route('storeBurger'), {
        onSuccess: () => {
            toast({
                title: 'Succès',
                description: `Burger "${form.name}" ajouté avec succès.`,
                variant: 'success',
            });
            form.reset(); // Réinitialiser les champs après succès
            selectedFile.value = null; // Réinitialiser le fichier
        },
        onError: () => {
            toast({
                title: 'Erreur',
                description: 'Une erreur est survenue. Veuillez vérifier les champs saisis.',
                variant: 'destructive',
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Burger Management', href: '/dashboard/burger' }]">
        <Head title="Burger management" />

        <SettingsLayout>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="outline">Add burger</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Add a New Burger</DialogTitle>
                        <DialogDescription>
                            Fill in the fields below to add a new burger.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleSubmit">
                        <!-- Nom du burger -->
                        <FormField name="name">
                            <FormItem>
                                <FormLabel>Name</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Name of the burger"
                                        v-model="form.name"
                                    />
                                </FormControl>
                                <FormMessage v-if="form.errors.name">
                                    {{ form.errors.name }}
                                </FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Prix -->
                        <FormField name="price">
                            <FormItem>
                                <FormLabel>Price</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Price"
                                        min="0"
                                        v-model="form.price"
                                    />
                                </FormControl>
                                <FormMessage v-if="form.errors.price">
                                    {{ form.errors.price }}
                                </FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Stock -->
                        <FormField name="stock">
                            <FormItem>
                                <FormLabel>Stock</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Stock"
                                        min="0"
                                        v-model="form.stock"
                                    />
                                </FormControl>
                                <FormMessage v-if="form.errors.stock">
                                    {{ form.errors.stock }}
                                </FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Description -->
                        <FormField name="description">
                            <FormItem>
                                <FormLabel>Description</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Description"
                                        v-model="form.description"
                                    />
                                </FormControl>
                                <FormMessage v-if="form.errors.description">
                                    {{ form.errors.description }}
                                </FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Image (Sélecteur de fichier) -->
                        <FormField name="image">
                            <FormItem>
                                <FormLabel>Image</FormLabel>
                                <FormControl>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="block w-full mt-1 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                        @change="(e) => selectedFile.value = e.target.files?.[0] || null"
                                    />
                                </FormControl>
                                <FormMessage v-if="form.errors.image">
                                    {{ form.errors.image }}
                                </FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Boutons -->
                        <DialogFooter>
                            <Button variant="outline" type="button" @click="form.reset()">Cancel</Button>
                            <Button type="submit">Save</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </SettingsLayout>
        <ListBurger></ListBurger>
    </AppLayout>
</template>
