
const angular = require('angular');

const baseURL = '/apps/autres/';

let app =
    angular.module("reportingApp", [])

        .config(["$interpolateProvider", function ($interpolateProvider) {
            //On change les symboles d'interpolation par défaut d'AngularJs
            $interpolateProvider.startSymbol('//').endSymbol('//');

        }])

        .controller("mainCtrl", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            //Lien de démarrage des tableaux par défaut
            $scope.tableUrl = baseURL + "table/init";
            //Lien de démarraage des filtres par défaut
            $scope.filterUrl = baseURL + "filter/init";

            //Champs en-tête du tableau
            $scope.headers = [];
            //Valeurs de remplissaage dans le tableau
            $scope.filtersData = [];

            //Affichage d'un loader si les données n'ont pas encore été reçues
            $scope.dataReady = true;

            //Filtres choisies
            $scope.chosenFilters = {};
            //Filters sur le côté
            $scope.filters = [
               /* {
                    id: 1,
                    lib: "Demande de contrats",
                    slug: 'demande_contrat',
                    active: false
                },*/
                {
                    id: 1,
                    lib: "Demande d'avis et conseils",
                    slug: 'avis',
                    active: false
                },
                {
                    id: 2,
                    lib: "Demande d'autorisations",
                    slug: 'autorisation',
                    active: false
                },
                {
                    id: 3,
                    lib: "Contrats",
                    slug: 'contrat',
                    active: false
                },
                {
                    id: 4,
                    lib: "Affaires contentieuses et litiges",
                    slug: 'litige',
                    active: false
                },
                {
                    id: 5,
                    lib: "Obligations",
                    slug: 'obligations',
                    active: false
                },
                {
                    id: 6,
                    lib: "Collaborateurs service juridique",
                    slug: 'collaborateurs_juridique',
                    active: false
                }
            ];

            //Afficher les filtres ?
            $scope.displayFilters = false;

            //Fonction pour nettoyer les filtres choisis
            $scope.clearFilters = function(){
                delete $scope.chosenFilters;
                $scope.chosenFilters = {};
            }

            $scope.launchFilter = function(filterId){
                //Activation de notre bouton et désactivation des autres
                for (let i = 0; i < $scope.filters.length; i++){
                    $scope.filters[i].active = false;
                }
                $scope.filters[filterId].active = true;


                $scope.filterUrl = baseURL + "filter/" + $scope.filters[filterId].slug;
                $scope.chosenFilters = {};
            }

            //Récupérer les données depuis le serveur
            $scope.fetchData = function(){
                //On prend le nom de l'objet recherché
                $scope.chosenFilters['processStr'] = false;
                $scope.filters.map(x => {
                   $scope.chosenFilters['processStr'] = x.active ? x.slug : $scope.chosenFilters['processStr'];
                });
                //Si aucun élément choisi, on ne fait rien
                if(!$scope.chosenFilters['processStr']){
                    return;
                }

                //On affiche le loader
                $scope.dataReady = false;

                //Envoi des données au serveur
                $http.post(baseURL + 'print', $scope.chosenFilters).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;

                        //Attente de 0.1 secondes avant tout affichage
                        $timeout(function(){
                            //Attribution des entêtes
                            $scope.headers = result.headers;
                            //Attribution des valeurs
                            $scope.filtersData = result.values;
                            //Récupération du tableau lié au données recherchés
                            $scope.tableUrl = baseURL + "table/" + $scope.chosenFilters.processStr;
                            //On cache le loader et on affiche les données
                            $scope.dataReady = true;
                        }, 100);
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                    });
            }

            //Récupérer les données depuis le serveur
            $scope.reportToMail = function(val){
                //On prend le nom de l'objet recherché
                $scope.chosenFilters['processStr'] = false;
                $scope.filters.map(x => {
                   $scope.chosenFilters['processStr'] = x.active ? x.slug : $scope.chosenFilters['processStr'];
                });
                //Si aucun élément choisi, on ne fait rien
                if(!$scope.chosenFilters['processStr']){
                    return;
                }
                //On ajoute le format de fichier
                $scope.chosenFilters['format'] = val;

                //On affiche le loader
                $scope.dataReady = false;

                //Envoi des données au serveur
                $http.post(baseURL + 'report-to-mail', $scope.chosenFilters).then(
                    //En cas de succès
                    function(response) {
                        //Code de statut
                        $scope.status = response.status;
                        //Données
                        $scope.data = response.data;
                        let result = $scope.data;

                        //On affiche le loader
                        $scope.dataReady = true;
                        alert('Mail envoyé')
                    },
                    //Erreur
                    function(response) {
                        $scope.data = response.data || 'Request failed';
                        $scope.status = response.status;
                    });
            }
        }]);
