# Journal des modifications

##### Juillet 2026
- Il est désormais possible d'indiquer plusieurs personnes ayant observé pour une même observation [#293](https://github.com/gloggi/qualix/issues/293)
- Il est désormais possible de supprimer la photo de profil d'un-e TN ou de sa propre personne [#336](https://github.com/gloggi/qualix/issues/336)
- Dans le jeu des noms, en mode "Facile (choix multiple)", un point est désormais attribué même si plusieurs participant·e·s du cours portent le même nom et que l'on clique sur l'autre option portant ce nom. En mode "Difficile (saisir le nom)", un nom saisi sans accent (par ex. "Muller" au lieu de "Müller") est désormais aussi compté comme correct [#382](https://github.com/gloggi/qualix/issues/382)
- La génération automatique de l'attribution des évaluations répartit désormais les évaluations de manière plus équilibrée au sein de l'équipe en cas de souhaits équivalents, au lieu de remplir d'abord les premiers membres de l'équipe jusqu'à leur capacité maximale [#394](https://github.com/gloggi/qualix/issues/394)

##### Juillet 2025
- Dans les évaluations, l'attribution des participant-e-s aux membres de l'équipe peut désormais être générée automatiquement en fonction des souhaits et des capacités de l'équipe et des participant-e-s. Pour cela, il suffit de cliquer sur la nouvelle icône "Générer l'attribution" sur une évaluation déjà créée, sous Administration -> Évaluations [#260](https://github.com/gloggi/qualix/issues/260)

##### Mai 2025
- Lors de l'invitation de personnes dans le cours, l'adresse e-mail saisie est désormais vérifiée plus strictement pour détecter les erreurs [#141](https://github.com/gloggi/qualix/issues/141)
- Dans l'éditeur d'évaluation, il est maintenant indiqué de manière encore plus claire et toujours visible que l'on est hors ligne et que les modifications n'ont donc pas encore pu être sauvegardées [#342](https://github.com/gloggi/qualix/issues/342)

##### Mars 2025
- Il est désormais possible d'importer des points de cours depuis eCamp v3 [#370](https://github.com/gloggi/qualix/pull/370)

##### Août 2024
- Il est désormais possible de créer et de remplir des grilles d'évaluation dans Qualix. Les grilles d'évaluation sont des catalogues de critères détaillés permettant d'évaluer de manière équitable et objective une performance complexe d'un-e participant-e. Dans la zone d'administration du cours, on peut concevoir / composer des grilles d'évaluation (c.-à-d. définir les critères du catalogue). Le questionnaire ainsi composé peut ensuite - pour une performance spécifique d'un-e ou plusieurs participant-e-s - être rempli directement en numérique dans Qualix, ou imprimé et rempli à la main [#343](https://github.com/gloggi/qualix/issues/343)

##### Juillet 2024
- Mise à niveau vers Laravel 11 et PHP >= 8.2.0 [#344](https://github.com/gloggi/qualix/pull/344)

##### Avril 2024
- Correction de bug : la toute première modification après l'ouverture de l'éditeur d'évaluation est de nouveau sauvegardée automatiquement [#320](https://github.com/gloggi/qualix/issues/320)

##### Mars 2024
- Après avoir ajouté une observation via la vue "Points de cours", on reste désormais sur le formulaire d'observation, afin de pouvoir saisir d'autres observations. Le comportement précédent datait de l'époque où le pense-bête et la vue "Points de cours" étaient encore séparés, et était optimisé pour des tâches d'observation avec exactement une observation par tâche. Le nouveau comportement est, espérons-le, plus utile pour saisir plusieurs petites observations [#334](https://github.com/gloggi/qualix/pull/334)
- Dans le générateur de groupes de participant-e-s, on peut désormais indiquer si certain-e-s participant-e-s doivent plutôt être réparti-e-s dans un plus grand ou un plus petit groupe. Cela peut par exemple être utile si des participant-e-s ne peuvent pas être présent-e-s tout le temps durant le cours [#335](https://github.com/gloggi/qualix/pull/335)

##### Février 2024
- Qualix contient désormais un jeu pour apprendre les noms ! Un lien vers le jeu se trouve sur la liste des participant-e-s [#332](https://github.com/gloggi/qualix/pull/332)

##### Janvier 2024
- Pour des raisons techniques et pratiques, le nombre d'exigences pertinentes dans une évaluation a été limité à 40 au maximum. La raison en est que le tableau récapitulatif ne pouvait sinon plus être affiché de manière sensée, tant techniquement que visuellement. D'un point de vue pédagogique également, le concept des évaluations dans Qualix n'est pas conçu pour contenir un très grand nombre d'exigences intégrées, car la clarté, l'esprit d'encouragement, la vérifiabilité, les secondes chances, la seconde formation, etc. en pâtissent tous. Nous voyons cela confirmé dans les passages suivants de la brochure RQF, qui montrent clairement qu'à chaque exigence minimale supplémentaire, la charge de travail augmente sensiblement, tant pour l'équipe de cours que pour les participant-e-s :
  > [Il] faut prendre en compte que chaque exigence minimale est associée à un moment d'observation, où les participant-e-s peuvent montrer ce qu'ils/elles ont appris et où l'équipe de cours peut le percevoir.[^1]

  > Durant le cours, les participant-e-s doivent absolument aussi pouvoir (continuer à) développer les compétences qui ne sont pas explicitement évaluées.[^1]

  > Il est également important que les participant-e-s aient la possibilité de s'exercer, d'essayer de nouvelles choses et de faire des erreurs, avant que les exigences minimales ne soient prises en compte et que la performance des participant-e-s ne soit évaluée.[^2]

  > Il faut s'assurer que, pour toutes les exigences minimales définies, les contenus correspondants soient également enseignés au cours du cours, et que les participant-e-s aient l'occasion de fournir les prestations attendues.[^3]

  > Chaque exigence minimale doit être remplie individuellement. Une compensation des faiblesses par des performances particulièrement bonnes dans d'autres domaines n'est donc pas possible.[^4]

  Pour ces raisons, nous recommandons l'utilisation de 10 exigences minimales au maximum dans un cours. L'interface utilisateur et les fonctionnalités de Qualix sont également optimisées sur la base de cette hypothèse.

- Lors de la création de nouvelles évaluations, seules les exigences explicitement marquées comme "exigence minimale" sont désormais présélectionnées, pour les raisons évoquées ci-dessus. Les autres exigences restent bien sûr sélectionnables.

[^1]: [Rückmelden, Qualifizieren und Fördern im Ausbildungskurs (en allemand), page 14](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom/14)
[^2]: [Rückmelden, Qualifizieren und Fördern im Ausbildungskurs (en allemand), page 15](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom/15)
[^3]: [Rückmelden, Qualifizieren und Fördern im Ausbildungskurs (en allemand), page 31](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom/31)
[^4]: [Rückmelden, Qualifizieren und Fördern im Ausbildungskurs (en allemand), page 35](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom/35)

##### Novembre 2023
- Il est désormais possible de télécharger les PDF d'évaluation pour tou-te-s les participant-e-s en même temps [#325](https://github.com/gloggi/qualix/pull/325)
- Des points de cours quotidiens "Divers" ou similaires peuvent désormais être générés automatiquement [#326](https://github.com/gloggi/qualix/issues/76)

##### Avril 2023
- Il est désormais possible d'accéder directement à la matrice d'évaluation via la navigation principale [#310](https://github.com/gloggi/qualix/issues/310)

##### Mars 2023
- Les filtres d'observation ont été améliorés et étendus. On peut désormais filtrer par auteur-e et par point de cours, ainsi que par plusieurs exigences et catégories simultanément [#307](https://github.com/gloggi/qualix/pull/307)
- Davantage de fonctionnalités de Qualix sont désormais configurables pour les copies de Qualix auto-hébergées. De plus, la sécurité du déploiement a été renforcée. Merci @cleverer ! [#308](https://github.com/gloggi/qualix/pull/308)

##### Février 2023
- Mise à niveau vers Laravel 10 et PHP >= 8.1.0 [#305](https://github.com/gloggi/qualix/issues/305)

##### Janvier 2023
- Les groupes de participant-e-s peuvent désormais être générés automatiquement directement dans Qualix. Les participant-e-s sont répartis de manière à éviter autant que possible de se retrouver plusieurs fois dans les mêmes groupes avec les mêmes autres participant-e-s. Le générateur de groupes de participant-e-s est disponible sous Administration -> Groupes de participant-e-s -> Générateur de groupes de participant-e-s [#301](https://github.com/gloggi/qualix/issues/301)

##### Décembre 2022
- Lorsqu'un cours est archivé, tous les groupes de participant-e-s et toutes les tâches d'observation sont désormais également supprimés, car ceux-ci peuvent potentiellement contenir des noms de participant-e-s, et la valeur ajoutée d'une consultation ultérieure serait perdue à cause de l'anonymisation [#298](https://github.com/gloggi/qualix/issues/298)
- Dans les cours archivés, les évaluations ne peuvent plus être modifiées ni créées, car dans de tels cours, aucun-e participant-e ne peut de toute façon plus être sélectionné-e.

##### Novembre 2022
- Les photos de l'équipe de cours sont désormais affichées sur l'aperçu, si au moins une personne a téléchargé une photo
- Les photos d'avatar non carrées des participant-e-s et de l'équipe de cours sont désormais rognées au lieu d'être déformées [#41](https://github.com/gloggi/qualix/issues/41)

##### Octobre 2022
- La fonction d'impression des évaluations a été retravaillée. Des fichiers PDF prêts à l'emploi, avec une mise en page cohérente, sont désormais directement téléchargés, au lieu de devoir utiliser la fonction d'impression PDF du navigateur. Comme nous ne dépendons plus du comportement des différents navigateurs, les marges du PDF ont en outre pu être réduites, afin d'économiser du papier [#228](https://github.com/gloggi/qualix/issues/228)
- Le design du bouton de connexion MiData a été adapté au nouveau design du MSdS. Merci @Sprudeel ! [#265](https://github.com/gloggi/qualix/issues/265)
- Nouveaux liens permettant de passer facilement au/à la participant-e suivant-e et précédent-e sur la vue détaillée d'un-e participant-e. Merci @Sprudeel ! [#274](https://github.com/gloggi/qualix/issues/274)
- Des optimisations de performance pour Qualix ont été rendues possibles. Merci @cleverer ! [#44](https://github.com/gloggi/qualix/issues/44)
- Corrections de bugs : lorsqu'une fonctionnalité (concrètement la connexion MiData et la synchronisation WebRTC) n'est pas configurée sur une instance de Qualix, elle est désormais complètement masquée / désactivée. Merci @cleverer ! [#286](https://github.com/gloggi/qualix/pull/286) [#284](https://github.com/gloggi/qualix/pull/284)

##### Septembre 2022
- Les vues "Points de cours" et "Pense-bête" ont été combinées. Les tâches d'observation sont désormais disponibles sous "Points de cours" [#165](https://github.com/gloggi/qualix/issues/165)
- Sous "Évaluations" dans le menu principal, une liste des évaluations (qui me sont assignées) peut désormais être affichée [#259](https://github.com/gloggi/qualix/issues/259)
- Expérimental : pour chaque évaluation avec exigences minimales, une matrice des exigences minimales peut être affichée. Celle-ci montre un aperçu de la progression de tou-te-s les participant-e-s dans toutes les exigences minimales, et permet de commenter le statut de réalisation de chaque exigence minimale. Ces commentaires sont également visibles dans l'éditeur d'évaluation, mais pas lors de l'impression de l'évaluation [#267](https://github.com/gloggi/qualix/pull/267)
- Il y a désormais un lien vers le journal des modifications sur la page d'accueil [#220](https://github.com/gloggi/qualix/issues/220)

##### Août 2022
- Il est désormais possible de définir, par cours, quels statuts les exigences peuvent avoir dans une évaluation. Les statuts peuvent être adaptés sous Administration -> Exigences -> Gérer les statuts... [#259](https://github.com/gloggi/qualix/issues/259)
- Les icônes ont été mises à jour. Pour les statuts d'exigence, il est désormais possible de choisir parmi un plus grand choix d'icônes. S'il te manque une icône, contacte l'équipe Qualix. Nous pouvons activer n'importe quelle icône de [cette liste](https://fontawesome.com/search?m=free&s=solid) [#264](https://github.com/gloggi/qualix/pull/264)

##### Juillet 2022
- Les "Qualis" ont été renommées en "Évaluations", afin de mieux communiquer à quoi elles servent, et pour qu'il soit plus clair que les observations ne sont pas optimisées pour la saisie des évaluations [#261](https://github.com/gloggi/qualix/issues/261)

##### Juin 2022
- Mise à niveau vers Laravel 9 et PHP >= 8.0.2 [#254](https://github.com/gloggi/qualix/pull/254)
- Correction de bug : plus aucune erreur ne survient désormais lorsqu'on archive ou supprime un cours contenant des participant-e-s sans photo

##### Mai 2022
- Lorsqu'on revient sur une évaluation avec le bouton retour, le contenu d'évaluation réellement le plus récent est désormais affiché [#250](https://github.com/gloggi/qualix/issues/250)
- Lors de l'insertion d'observations dans une évaluation, il est désormais possible de filtrer en option les observations déjà utilisées dans l'évaluation (merci pour l'idée @Tschet1) [#230](https://github.com/gloggi/qualix/issues/230)

##### Avril 2022
- Ajout d'un lien de contact dans le pied de page [#233](https://github.com/gloggi/qualix/issues/233)
- Lors de la saisie d'observations, la limite de caractères existante est désormais rendue visible [#223](https://github.com/gloggi/qualix/issues/223)
- Dans le pense-bête, les noms des tâches d'observation sont affichés lorsqu'on passe la souris sur une photo de participant-e [#210](https://github.com/gloggi/qualix/issues/210)
- Après la création d'observations pour plusieurs participant-e-s en même temps, des liens vers tou-te-s les participant-e-s observé-e-s sont désormais affichés [#217](https://github.com/gloggi/qualix/issues/217)

##### Mars 2022
- Jusqu'à 65535 caractères peuvent désormais être saisis dans le champ libre des participant-e-s. La raison est que dans MiData, la recommandation du/de la participant-e peut également atteindre cette longueur maximale [#247](https://github.com/gloggi/qualix/issues/247)

##### Février 2022
- L'import des participant-e-s gère désormais mieux les cas où des colonnes manquent ou sont dans un ordre différent de celui attendu, et donne des retours plus précis sur ce qui ne va pas exactement.

##### Janvier 2022
- Les évaluations peuvent désormais être affichées sur la page d'aperçu [#242](https://github.com/gloggi/qualix/pull/242)
- La case à cocher "Exigence minimale" pour les nouvelles exigences est désormais activée par défaut, afin d'encourager les critères stricts comme bonne pratique.
- Mises à jour de sécurité

##### Décembre 2021
- Correction de bug : les observations sont désormais triées correctement partout par point de cours, y compris pour les numéros de point de cours / de jour à deux chiffres [#214](https://github.com/gloggi/qualix/issues/214)

##### Novembre 2021
- Correction de bug dans l'équipe : les invitations peuvent de nouveau être supprimées (merci pour le signalement @mario-zelger) [#232](https://github.com/gloggi/qualix/issues/232)

##### Octobre 2021
- Correction de bug dans l'éditeur d'évaluation : dans les évaluations ne contenant aucune exigence, le contenu de l'éditeur n'est plus dupliqué à la réouverture (merci pour le signalement @Tschet1) [#223](https://github.com/gloggi/qualix/issues/229)

##### Septembre 2021
- Le contenu de l'éditeur d'évaluation est désormais sauvegardé automatiquement dès que 2 secondes s'écoulent sans saisie. Afin que cela ne cause pas de conflits avec d'autres utilisateurs/utilisatrices, le contenu de l'éditeur d'évaluation est en outre synchronisé entre tous les navigateurs ayant ouvert la même évaluation. La synchronisation se fait via une connexion directe chiffrée de bout en bout entre les navigateurs [#221](https://github.com/gloggi/qualix/issues/221)

##### Août 2021
- Les photos de profil des membres de l'équipe sont désormais affichées dans la zone d'administration du cours

##### Juin 2021
- Sauf en cas d'urgence, les mises à jour sur https://qualix.flamberg.ch ne sont désormais déployées que la nuit, afin de ne pas perturber les cours en cours [#149](https://github.com/gloggi/qualix/issues/149)

##### Mai 2021
- Correction : la connexion via hitobito fonctionne désormais aussi si aucun nom scout n'a été renseigné dans MiData [#199](https://github.com/gloggi/qualix/pull/199)

##### Avril 2021
- Tou-te-s les participant-e-s ont désormais un champ de texte libre optionnel, pouvant par exemple être utilisé pour des points d'encouragement. Le texte libre est affiché sur la page de détail du/de la participant-e. [#191](https://github.com/gloggi/qualix/pull/191)
- Les imports de points de cours et de participant-e-s acceptent désormais une plus large palette de formats de fichiers. Pour les CSV, l'encodage est deviné automatiquement, afin que les caractères accentués et autres caractères spéciaux soient importés de manière plus fiable. [#192](https://github.com/gloggi/qualix/pull/192)

##### Mars 2021
- L'impression subjective sur les observations peut être désactivée dans les paramètres du cours. Les champs optionnels (impression, exigences, catégories) ne sont plus affichés s'ils ne sont pas utilisés dans le cours. [#189](https://github.com/gloggi/qualix/pull/189)

##### Février 2021
- Une évaluation d'un-e participant-e peut désormais être assignée à plusieurs membres de l'équipe [#178](https://github.com/gloggi/qualix/pull/178)
- Diverses améliorations de l'éditeur d'évaluation [#186](https://github.com/gloggi/qualix/pull/186)
- Dans la sélection lors de la saisie d'une observation, les points de cours sont désormais repoussés vers le bas s'ils datent d'avant la veille [#188](https://github.com/gloggi/qualix/pull/188)

##### Janvier 2021
- Les seuils pour les marquages rouges et verts sont désormais réglables par cours [#173](https://github.com/gloggi/qualix/pull/173)

##### Décembre 2020
- Introduction du journal des modifications [#167](https://github.com/gloggi/qualix/pull/167)

##### Octobre 2020
- Tâches d'observation [#147](https://github.com/gloggi/qualix/pull/147)
- Saisie et impression des évaluations [#146](https://github.com/gloggi/qualix/pull/146)

##### Septembre 2020
- Mises à jour de sécurité [#143](https://github.com/gloggi/qualix/pull/143) [#144](https://github.com/gloggi/qualix/pull/144)
- Groupes de participant-e-s [#142](https://github.com/gloggi/qualix/pull/142)
- Améliorations de sécurité [#140](https://github.com/gloggi/qualix/pull/140)

##### Août 2020
- Correction du titre erroné lors de la modification d'une observation (merci @diegosteiner) [#139](https://github.com/gloggi/qualix/pull/139)

##### Juillet 2020
- Mise à niveau vers Laravel 7 et PHP >= 7.2.5 [#137](https://github.com/gloggi/qualix/pull/137)

##### Juin 2020
- Import des participant-e-s depuis une liste MiData [#136](https://github.com/gloggi/qualix/pull/136)

##### Avril 2020
- Lors de la création d'exigences, des points de cours peuvent désormais être liés directement [#125](https://github.com/gloggi/qualix/pull/125)

##### Mars 2020
- Mises à jour de sécurité [#126](https://github.com/gloggi/qualix/pull/126) [#127](https://github.com/gloggi/qualix/pull/127) [#128](https://github.com/gloggi/qualix/pull/128)
- Les "critères éliminatoires" ont été renommés en "exigences minimales" [#123](https://github.com/gloggi/qualix/pull/123)

##### Février 2020
- Les fonctionnalités les plus récentes de Qualix sont automatiquement déployées sur https://qualix.flamberg.ch [#113](https://github.com/gloggi/qualix/pull/113)

##### Janvier 2020
- Observations multi-participant-e-s [#106](https://github.com/gloggi/qualix/pull/106)
- Import de points de cours depuis eCamp v2 [#107](https://github.com/gloggi/qualix/pull/107)
- Restauration des saisies de formulaire après une déconnexion automatique pour cause de timeout [#105](https://github.com/gloggi/qualix/pull/105)

##### Décembre 2019
- Traduction en français [#99](https://github.com/gloggi/qualix/pull/99)
- Mises à jour de sécurité [#100](https://github.com/gloggi/qualix/pull/100) [#101](https://github.com/gloggi/qualix/pull/101)

##### Novembre 2019
- Introduction de la licence MIT pour le logiciel [#90](https://github.com/gloggi/qualix/pull/90)

##### Octobre 2019
- Correctif de sécurité [#89](https://github.com/gloggi/qualix/pull/89)

##### Septembre 2019
- Pense-bête quotidien [#88](https://github.com/gloggi/qualix/pull/88)
- Connexion MiData [#87](https://github.com/gloggi/qualix/pull/87)

##### Juillet 2019
- Textes d'aide à divers endroits de l'interface utilisateur [#85](https://github.com/gloggi/qualix/pull/85)

##### Juin 2019
- Le premier champ ou le champ le plus important d'un formulaire est désormais sélectionné automatiquement [#81](https://github.com/gloggi/qualix/pull/81)
- Archivage et suppression de cours [#82](https://github.com/gloggi/qualix/pull/82)

##### Avril 2019
- Sortie de Qualix en tant que réimplémentation de l'outil Quali du Flamberg [#30](https://github.com/gloggi/qualix/pull/30)
