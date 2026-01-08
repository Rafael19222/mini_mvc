-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 08 jan. 2026 à 12:44
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mini_mvc`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `date_order` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id`, `id_product`, `id_user`) VALUES
(1, 10, 2),
(2, 3, 3),
(3, 11, 3),
(4, 6, 3);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `image` varchar(2555) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `nom`, `prix`, `description`, `image`) VALUES
(1, 'Booster Pokémon Écarlate & Violet', 7, 'Booster de 10 cartes Pokémon', 'https://m.media-amazon.com/images/I/71aT0SO2KtL.jpg'),
(2, 'ETB Astres Radieux', 60, 'Coffret Dresseur d Elite pour collectionneurs', 'https://m.media-amazon.com/images/I/710K8Moy-EL._AC_SX679_.jpg'),
(3, 'Elite Trainer Box ME02 - Flammes Fantasmagoriques', 150, 'ETB ', 'https://pokeleman.fr/cdn/shop/files/Pokemon_Coffret_upc_Mega-Evolution_Flammes_Fantasmagoriques.png?v=1761651354&width=2048'),
(4, 'Carte Pokémon Pikachu van gogh', 20, 'Carte Pokémon de Pikachu ', 'https://m.media-amazon.com/images/I/71Vd+Uv+XyL.jpg'),
(5, 'Boîte Pokémon Premium Dracaufeu VMAX', 500, 'Coffret premium avec cartes promo et boosters', 'https://www.relictcg.com/cdn/shop/articles/pokluxnov22_box3d_20220816.png?v=1706026984'),
(6, 'Bundle Fable nebuleuse', 25, '6 boosters', 'https://m.media-amazon.com/images/I/91eVou9c1kL._AC_SX679_.jpg'),
(7, 'Tin Box Zamazenta', 25, 'Coffret métal avec carte promo Zamazenta V', 'https://pokeleman.fr/cdn/shop/files/coffret-collection-printemps-valisette-boite-a-gouter-zacian-zamazenta-epee-et-bouclier.png?v=1682584483&width=2048'),
(8, 'Starter Deck Épée & Bouclier', 15, 'Deck prêt à jouer pour débutants Pokémon TCG', 'https://m.media-amazon.com/images/I/81zYR0g3pbL._AC_UF1000,1000_QL80_.jpg'),
(9, 'Carte Pokémon Evoli ', 30, 'Carte officielle Evoli ex Terra crystal', 'https://www.dexocard.com/img/cards/sv85/fr_high_56991a2c9a61548f602b594c704cba69.webp?1737122491'),
(10, 'Carte Pokémon Gengar ', 28, 'Carte de Gengar ex, version officielle Pokémon Center', 'https://m.media-amazon.com/images/I/51fQlzphzWL._AC_UF1000,1000_QL80_.jpg'),
(11, 'Pokéball Plus', 5000, 'Accessoire connecté compatible Nintendo Switch', 'https://pokemonletsgo.pokemon.com/assets/img/pokeball-plus/pokeball-plus-device.png'),
(12, 'Bundle prismatique', 90, 'Bundle de 6 boosters', 'https://fr.shopping.rakuten.com/pictures/019983a2-9ed7-728c-96ce-defef7cbef54_L_NOPAD.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `email`, `password`) VALUES
(1, 'Bidoof', 'Bidoofgoat@gmail.com', '$2y$10$Igdj71uYPKiJLw06Jt552epszSUVQo4AO8t1/ECVQSeC88Zv6A.qW'),
(2, 'Dupont de ligonnes', 'rafael.oliveira-spinola@efrei.net', '$2y$10$pxqsBS2k4RCD/tZHYCsuReVsGJKGpZoRZVL6qxKCzg8ktfSNJGarK'),
(3, 'hugo', 'hugo@hugo.com', '$2y$10$xbSO08XLRzpYcYyzjAgdUOpEzgk5gHAjcGNdAh3s4MEyPYHIbitfy');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_panier_user` (`id_user`),
  ADD KEY `fk_panier_produit` (`id_product`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `fk_panier_produit` FOREIGN KEY (`id_product`) REFERENCES `produit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_panier_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
