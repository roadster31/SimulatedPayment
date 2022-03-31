# SimulatedPayment

Il s'agit d'un pseudo-module de paiement à utiliser pendant vos tests.

Il permet de simuler un paiement par formulaire (type CB), en utilisant les mécanismes classiques de Thelia,
et de choisir le résultat du paiement. Vous pouvez ainsi facilement valider vos pages de succès et d'échec de paiement.

Une fois que vous avez déclenché le paiement, le module vous permet de choisir entre un paiement réussi et un 
échec du paiement.

Pour l'installation : `composer require roadster31/simulated-payment-module` 

