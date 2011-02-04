<?php
class CDO {
	public function __construct ($sDsn, $sUsername = null, $sPassword = null, $aDriverOption) {
		
	}
}
/*
	beginTransaction - commence une transaction
	commit - valide une transaction
	errorCode - récupère un code erreur, s'il y en a, depuis la base de données
	errorInfo - récupère un tableau contenant les informations sur l'erreur, s'il y en a, depuis la base de données
	exec - exécute une requête SQL et retourne le nombre de lignes affectées
	getAttribute - récupère un attribut d'une connexion à une base de données
	lastInsertId - récupère la valeur de la dernière ligne insérée dans une table
	prepare - prépare une requête SQL pour exécution
	query - exécute une requête SQL et retourne le jeu de résultats
	quote - retourne une version protégée d'une chaîne pour utilisation dans une requête SQL
	rollBack - annule une transaction
	setAttribute - définit un attribut d'une connexion à une base de données
*/

/*
PDO::PARAM_BOOL (entier) Représente le type de données booléen. 
PDO::PARAM_NULL (entier) Représente le type de données NULL SQL. 
PDO::PARAM_INT (entier) Représente le type de données INTEGER SQL. 
PDO::PARAM_STR (entier) Représente les types de données CHAR, VARCHAR ou les autres types de données sous forme de chaîne de caractères SQL. 
PDO::PARAM_LOB (entier) Représente le type de données "objet large" SQL. 
PDO::PARAM_STMT (entier) Représente un type de jeu de résultats. N'est actuellement pas supporté par tous les drivers. 
PDO::PARAM_INPUT_OUTPUT (entier) Spécifie que le paramètre est un paramètre INOUT pour une procédure stockée. Vous devez utiliser l'opérateur OR avec un type de données explicite PDO::PARAM_*. 
PDO::FETCH_LAZY (entier) Spécifie que la méthode de récupération doit retourner chaque ligne en tant qu'objet avec les noms de variables correspondant aux noms des colonnes retournées dans le jeu de résultats. PDO::FETCH_LAZY crée les noms des variables de l'objet comme ils sont rencontrés. 
PDO::FETCH_ASSOC (entier) Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes comme elles sont retournées dans le jeu de résultats correspondant. Si le jeu de résultats contient de multiples colonnes avec le même nom, PDO::FETCH_ASSOC retourne une seule valeur par nom de colonne. 
PDO::FETCH_NAMED (entier) Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes comme elles sont retournées dans le jeu de résultats correspondant. Si le jeu de résultats contient de multiples colonnes avec le même nom, PDO::FETCH_NAMED retourne un tableau de valeurs par nom de colonne. 
PDO::FETCH_NUM (entier) Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par le numéro des colonnes comme elles sont retournées dans le jeu de résultats correspondant, en commençant à 0. 
PDO::FETCH_BOTH (entier) Spécifie que la méthode de récupération doit retourner chaque ligne dans un tableau indexé par les noms des colonnes ainsi que leurs numéros, comme elles sont retournées dans le jeu de résultats correspondant, en commençant à 0. 
PDO::FETCH_OBJ (entier) Spécifie que la méthode de récupération doit retourner chaque ligne dans un objet avec les noms de propriétés correspondant aux noms des colonnes comme elles sont retournées dans le jeu de résultats. 
PDO::FETCH_BOUND (entier) Spécifie que la méthode de récupération doit retourner TRUE et assigner les valeurs des colonnes du jeu de résultats dans les variables PHP auxquelles elles sont liées avec la méthode PDOStatement::bindParam() ou la méthode PDOStatement::bindColumn(). 
PDO::FETCH_COLUMN (entier) Spécifie que la méthode de récupération doit retourner uniquement une seule colonne demandée depuis la prochaine ligne du jeu de résultats. 
PDO::FETCH_CLASS (entier) Spécifie que la méthode de récupération doit retourner une nouvelle instance de la classe demandée, liant les colonnes aux propriétés nommées dans la classe. 
PDO::FETCH_INTO (entier) Spécifie que la méthode de récupération doit mettre à jour une instance existante de la classe demandée, liant les colonnes aux propriétés nommées dans la classe. 
PDO::FETCH_FUNC (entier)
PDO::FETCH_GROUP (entier)
PDO::FETCH_UNIQUE (entier)
PDO::FETCH_KEY_PAIR (entier) Récupération dans un tableau lorsque la première colonne est une clé et tous les autres colonnes sont les valeurs 
PDO::FETCH_CLASSTYPE (entier)
PDO::FETCH_SERIALIZE (entier) Disponible depuis PHP 5.1.0. 
PDO::FETCH_PROPS_LATE (entier) Disponible depuis PHP 5.2.0 
PDO_ATTR_AUTOCOMMIT (entier) Si la valeur vaut FALSE, PDO tente de désactiver l'auto-validation lorsque la connexion commence une transaction. 
PDO::ATTR_PREFETCH (entier) Définir la taille de la pré-récupération vous permet d'accroître les performances de votre application. Toutes les combinaisons bases de données / drivers ne supportent pas cette fonctionnalité. Ceci accroît les performances au détriment de la consommation de mémoire vive. 
PDO::ATTR_TIMEOUT (entier) Définit la valeur d'attente en secondes pour les communications avec la base de données. 
PDO::ATTR_ERRMODE (entier) Voir la section sur les erreurs et la gestion des erreurs pour plus d'informations sur cet attribut. 
PDO::ATTR_SERVER_VERSION (entier) Attribut en lecture seule ; il retourne des informations sur la version de la base de données à laquelle PDO est connecté. 
PDO::ATTR_CLIENT_VERSION (entier) Attribut en lecture seule ; il retourne des informations sur la version de la bibliothèque cliente utilisée par PDO. 
PDO::ATTR_SERVER_INFO (entier) Attribut en lecture seule ; il retourne quelques meta-informations sur le serveur de base de données auquel PDO est connecté. 
PDO::ATTR_CONNECTION_STATUS (entier)
PDO::ATTR_CASE (entier) Force les noms des colonnes dans une casse spécifiée par les constantes PDO::CASE_*. 
PDO::ATTR_CURSOR_NAME (entier) Récupère ou définit le nom à utiliser pour un curseur. Très utile lors de l'utilisation de curseurs scrollables et des mises à jour positionnées. 
PDO::ATTR_CURSOR (entier) Sélectionne le type de curseur. PDO supporte actuellement soit PDO::CURSOR_FWDONLY, soit PDO::CURSOR_SCROLL. Conserver PDO::CURSOR_FWDONLY tant que vous savez que vous avez besoin d'un curseur scrollable. 
PDO::ATTR_DRIVER_NAME (chaîne de caractères) Retourne le nom du driver.
PDO::ATTR_ORACLE_NULLS (entier) Convertit les chaînes vides en valeurs NULL SQL dans les données récupérées. 
PDO::ATTR_PERSISTENT (entier) Demande une connexion persistante, plutôt que de créer une nouvelle connexion. Voir les connexions et le gestionnaire de connexion pour plus d'informations sur cet attribut. 
PDO::ATTR_STATEMENT_CLASS (entier)
PDO::ATTR_FETCH_CATALOG_NAMES (entier) Ajoute le contenu du catalogue de noms dans chaque nom de colonnes retourné dans le jeu de résultat. Le catalogue de noms et les noms de colonnes sont séparés par un point (.). Le support de cet attribut n'est pas disponible pour tous les drivers ; il peut ne pas être disponible pour votre driver. 
PDO::ATTR_FETCH_TABLE_NAMES (entier) Ajoute le contenu de la table de noms dans chaque nom de colonne retourné dans le jeu de résultats. La table de nom et les noms de colonnes sont séparés par un point (.). Le support de cet attribut n'est pas disponible pour tous les drivers ; il peut ne pas être disponible pour votre driver. 
PDO::ATTR_STRINGIFY_FETCHES (entier)
PDO::ATTR_MAX_COLUMN_LEN (entier)
PDO::ATTR_DEFAULT_FETCH_MODE (entier) Disponible depuis PHP 5.2.0. 
PDO::ATTR_EMULATE_PREPARES (entier) Disponible depuis PHP 5.1.3. 
PDO::ERRMODE_SILENT (entier) N'envoie pas d'erreur ni d'exception si une erreur survient. Le développeur doit explicitement vérifier les erreurs. C'est le mode par défaut. Voir les erreurs et la gestion des erreurs pour plus d'informations sur cet attribut. 
PDO::ERRMODE_WARNING (entier) Envoie une erreur de niveau E_WARNING si une erreur survient. Voir les erreurs et la gestion des erreurs pour plus d'informations sur cet attribut. 
PDO::ERRMODE_EXCEPTION (entier) Lance une exception PDOException si une erreur survient. Voir les erreurs et la gestion des erreurs pour plus d'informations sur cet attribut. 
PDO::CASE_NATURAL (entier) Laisse les noms de colonnes comme retournés par le driver de base de données. 
PDO::CASE_LOWER (entier) Force les noms de colonnes en minuscule. 
PDO::CASE_UPPER (entier) Force les noms des colonnes en majuscule. 
PDO::NULL_NATURAL (entier)
PDO::NULL_EMPTY_STRING (entier)
PDO::NULL_TO_STRING (entier)
PDO::FETCH_ORI_NEXT (entier) Récupère la prochaine ligne d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::FETCH_ORI_PRIOR (entier) Récupère la ligne précédente d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::FETCH_ORI_FIRST (entier) Récupère la première ligne d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::FETCH_ORI_LAST (entier) Récupère la dernière ligne d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::FETCH_ORI_ABS (entier) Récupère la ligne demandée par un numéro de ligne d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::FETCH_ORI_REL (entier) Récupère la ligne demandée par une position relative à la position courante du curseur d'un jeu de résultats. Valide seulement pour les curseurs scrollables. 
PDO::CURSOR_FWDONLY (entier) Crée un objet PDOStatement avec un curseur uniquement de retour. C'est le choix par défaut pour le curseur, car il est rapide et l'accès aux données est commun pour les masques en PHP. 
PDO::CURSOR_SCROLL (entier) Crée un objet PDOStatement avec un curseur scrollable. Passez la constante PDO::FETCH_ORI_* pour contrôler les lignes récupérées du jeu de résultats. 
PDO::ERR_NONE (chaîne de caractères) Correspond à SQLSTATE '00000', ce qui signifie que la requête SQL a réussi sans erreur, ni avertissement. Cette constante est utile lorsque vous utilisez PDO::errorCode() ou PDOStatement::errorCode() pour déterminer si une erreur est survenue. Cependant, vous devez déjà savoir si c'est le cas en examinant le code retourné par la méthode qui a lancée l'erreur. 
PDO::PARAM_EVT_ALLOC (entier) Alloue un événement 
PDO::PARAM_EVT_FREE (entier) Désalloue un événement 
PDO::PARAM_EVT_EXEC_PRE (entier) Toujours faire un trigger avant l'éxécution d'une requête préparée. 
PDO::PARAM_EVT_EXEC_POST (entier) Toujours effectuer un trigger de sous séquence avant l'exécution d'une requête préparée. 
PDO::PARAM_EVT_FETCH_PRE (entier) Toujours effectuer un trigger avant de récupérer un résultat d'un jeu de résultats. 
PDO::PARAM_EVT_FETCH_POST (entier) Toujours effectuer un trigger de sous séquence avant de récupérer un résultat d'un jeu de résultats. 
PDO::PARAM_EVT_NORMALIZE (entier) Toujours effectuer un trigger lors de l'enregistrement des paramètres liés permettant au driver de normaliser le nom des paramètres.
*/ 
?>