-- Migración para agregar soporte para NFTs musicales

-- Tabla de NFTs
CREATE TABLE IF NOT EXISTS nfts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    song_id INT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    blockchain VARCHAR(50) NOT NULL,
    token_address VARCHAR(255) NOT NULL,
    token_id VARCHAR(255) NOT NULL,
    price DECIMAL(18, 8) NOT NULL,
    currency VARCHAR(10) DEFAULT 'ETH',
    is_for_sale BOOLEAN DEFAULT TRUE,
    status ENUM('active', 'sold', 'removed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE SET NULL,
    UNIQUE KEY unique_token (blockchain, token_address, token_id)
);

-- Tabla de ventas de NFTs
CREATE TABLE IF NOT EXISTS nft_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nft_id INT NOT NULL,
    seller_id INT NOT NULL,
    buyer_id INT NOT NULL,
    price DECIMAL(18, 8) NOT NULL,
    currency VARCHAR(10) DEFAULT 'ETH',
    transaction_hash VARCHAR(255) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nft_id) REFERENCES nfts(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_transaction (transaction_hash)
);

-- Tabla de colecciones de NFTs
CREATE TABLE IF NOT EXISTS nft_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    blockchain VARCHAR(50) NOT NULL,
    contract_address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Relación entre NFTs y colecciones
ALTER TABLE nfts ADD COLUMN collection_id INT NULL;
ALTER TABLE nfts ADD FOREIGN KEY (collection_id) REFERENCES nft_collections(id) ON DELETE SET NULL;
