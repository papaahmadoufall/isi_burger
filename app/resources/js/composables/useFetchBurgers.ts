import { ref } from 'vue';

// Fonction pour récupérer les données des burgers
export function useFetchBurgers() {
    const burgers = ref<any[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const fetchBurgers = async () => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await fetch('http://localhost:8000/burgers');
            if (!response.ok) {
                throw new Error(`Erreur lors de la récupération des burgers : ${response.statusText}`);
            }

            const data = await response.json();
            burgers.value = data; // Assurez-vous que l'API retourne un tableau de burgers
        } catch (err: any) {
            error.value = err.message || 'Erreur inconnue';
        } finally {
            isLoading.value = false;
        }
    };

    // Retourne les données, l'état de chargement et la fonction de récupération
    return {
        burgers,
        isLoading,
        error,
        fetchBurgers,
    };
}
