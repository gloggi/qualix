# Journal des modifications

##### Avril 2026
- Amélioration de la sécurité des photos de participant·e·s : les photos sont désormais servies de manière protégée. Seuls les membres d’équipe connectés du cours concerné peuvent y accéder, et plus via un lien direct.

  ⚠️ Pour les instances Qualix existantes :
  Les photos déjà téléchargées sont encore stockées dans un espace public et doivent être migrées une fois. Pour cela, la commande Artisan suivante peut être exécutée :

  ```bash
  php artisan qualix:migrate-participant-images
  ```

TODO translate CHANGELOG.md, making sure to use qualix terminology
