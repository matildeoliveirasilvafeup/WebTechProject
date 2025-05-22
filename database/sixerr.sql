PRAGMA foreign_keys = ON;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT CHECK(role IN ('user', 'admin')) DEFAULT 'user',
    is_banned INTEGER DEFAULT 0 CHECK (is_banned IN (0, 1)),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE deleted_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT NOT NULL,
    created_at TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason TEXT NOT NULL
);

CREATE TABLE profiles (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    bio TEXT,
    profile_picture TEXT,
    location TEXT DEFAULT 'Portugal',
    is_freelancer INTEGER DEFAULT 0,
    is_client INTEGER DEFAULT 1
);

CREATE TABLE favorites (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    listing_id INTEGER REFERENCES service(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, listing_id)
);

CREATE TABLE profiles_preferences (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    language TEXT DEFAULT '',
    proficiency TEXT DEFAULT '',
    communication TEXT DEFAULT '',
    preferred_days_times JSONB DEFAULT '{}'
);

CREATE TABLE conversations (
    id VARCHAR(50),
    service_id INTEGER,
    user1_id INTEGER NOT NULL,
    user2_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id, service_id)
);

CREATE TABLE messages (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    conversation_id VARCHAR(50) NOT NULL,
    service_id INTEGER NOT NULL,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    message TEXT NOT NULL,
    sub_message TEXT DEFAULT '',
    file TEXT,
    is_read INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id, service_id)
        REFERENCES conversations(id, service_id)
        ON DELETE CASCADE
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    freelancer_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    description TEXT,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    subcategory_id INTEGER REFERENCES subcategories(id) ON DELETE SET NULL,
    price REAL NOT NULL,
    delivery_time INTEGER NOT NULL,
    number_of_revisions INTEGER DEFAULT 1,
    language TEXT DEFAULT 'English',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    average_rating REAL DEFAULT 0,
    total_reviews INTEGER DEFAULT 0,
    favorites_count INTEGER DEFAULT 0
);

CREATE TABLE payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER NOT NULL,
    client_id INTEGER NOT NULL,
    freelancer_id INTEGER NOT NULL,
    method TEXT NOT NULL,
    billing_name TEXT NOT NULL,
    billing_email TEXT NOT NULL,
    billing_address TEXT NOT NULL,
    billing_city TEXT NOT NULL,
    billing_postal TEXT NOT NULL,
    status TEXT DEFAULT 'Completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE service_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    media_url TEXT NOT NULL
);

CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    client_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    hiring_id INTEGER UNIQUE REFERENCES hirings(id) ON DELETE CASCADE,
    rating INTEGER CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    icon TEXT,
    name TEXT UNIQUE NOT NULL
);

CREATE TABLE subcategories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER REFERENCES categories(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    UNIQUE(category_id, name)
);

CREATE TABLE hirings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    client_id INTEGER,
    owner_id INTEGER,
    status TEXT NOT NULL DEFAULT 'Pending' CHECK (status IN ('Pending', 'Accepted', 'Rejected', 'Cancelled', 'Completed', 'Closed')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP
);

INSERT INTO users (name, username, email, password_hash, role) VALUES
('João Silva', 'joaosilva', 'joao@example.com', 'hash1', 'user'),
('Maria Costa', 'mariac', 'maria@example.com', 'hash2', 'admin'),
('Ricardo Melo', 'ricardomelo', 'ricardo@example.com', 'hash3', 'user'),
('Ana Sousa', 'anasousa', 'ana@example.com', 'hash4', 'user');


INSERT INTO profiles (user_id, bio, profile_picture, is_freelancer, is_client) VALUES
(1, 'Designer gráfico com 5 anos de experiência.', 'https://picsum.photos/200?1', 1, 1),
(2, 'Gestora de plataforma.', 'https://picsum.photos/200?2', 0, 1),
(3, 'Desenvolvedor web especializado em front-end.', 'https://picsum.photos/200?3', 1, 1),
(4, 'Empresária à procura de serviços criativos.', 'https://picsum.photos/200?4', 0, 1);


INSERT INTO categories (name, icon) VALUES
('Graphics & Design', 'fas fa-paint-brush'),
('Digital Marketing', 'fas fa-bullhorn'),
('Writing & Translation', 'fas fa-pencil-alt'),
('Programming & Tech', 'fas fa-laptop-code'),
('Video & Animation', 'fas fa-video'),
('AI Services', 'fas fa-robot'),
('Music & Audio', 'fas fa-headphones'),
('Business', 'fas fa-briefcase'),
('Consulting', 'fas fa-users-cog');

INSERT INTO subcategories (category_id, name) VALUES
(1, 'Logos'),
(1, 'Business Cards'),
(1, 'Brand Identity'),
(1, 'Social Media Design'),
(1, 'Flyers & Posters'),
(1, 'Illustration'),
(1, 'Presentation Design'),

(2, 'Landing Pages'),
(2, 'Email Marketing'),
(2, 'SEO'),
(2, 'Online Advertising'),
(2, 'Social Media Strategy'),
(2, 'Content Marketing'),

(3, 'Creative Writing'),
(3, 'Translations'),
(3, 'Proofreading & Editing'),
(3, 'Technical Writing'),
(3, 'Copywriting'),
(3, 'Product Descriptions'),

(4, 'Web Development'),
(4, 'Mobile Apps'),
(4, 'Automation'),
(4, 'Scripts & Bots'),
(4, 'API Integration'),
(4, 'Database Development'),

(5, 'Explainer Videos'),
(5, '2D Animation'),
(5, '3D Animation'),
(5, 'Video Editing'),
(5, 'Logo Animation'),
(5, 'Subtitles & Captions'),

(6, 'Image Generation'),
(6, 'Chatbots'),
(6, 'Machine Learning Models'),
(6, 'AI Data Analysis'),
(6, 'Voice Cloning'),
(6, 'AI Assistants'),

(7, 'Voice Over'),
(7, 'Music Production'),
(7, 'Mixing & Mastering'),
(7, 'Podcast Editing'),
(7, 'Sound Effects'),
(7, 'Jingles & Intros'),

(8, 'Business Plans'),
(8, 'Market Research'),
(8, 'Financial Consulting'),
(8, 'Pitch Decks'),
(8, 'Virtual Assistant'),
(8, 'Data Entry'),

(9, 'Career Coaching'),
(9, 'Startup Consulting'),
(9, 'Legal Consulting'),
(9, 'Marketing Strategy'),
(9, 'Tech Consulting'),
(9, 'HR & Recruiting');

INSERT INTO services (freelancer_id, title, description, category_id, subcategory_id, price, delivery_time, number_of_revisions, language, average_rating, total_reviews) VALUES
(1, 'Unique Logo Design', 'Custom logo tailored to your brand identity.', 1, 1, 75.00, 3, 2, 'Italian/French', 4.75, 4),
(3, 'Professional Landing Page', 'Modern, responsive landing page design.', 2, 8, 150.00, 5, 3, 'English', 4.00, 2),
(1, 'Business Card Design', 'Stylish and modern business cards for your brand.', 1, 2, 40.00, 2, 1, 'French', 5.00, 2),
(1, 'Company Website in HTML/CSS', 'Responsive website with up to 5 pages.', 2, 4, 200.00, 7, 4, 'Spanish/English', 3.00, 1),
(1, 'Social Media Template Pack', 'Set of 10 customizable Instagram templates.', 1, 5, 60.00, 3, 2, 'Portuguese', 4.00, 1),
(3, 'Landing Page with Contact Form', 'Lead-optimized landing page with working contact form.', 2, 9, 130.00, 4, 2, 'English', 5.00, 1),
(1, 'Brand Identity Manual', 'Complete visual identity guide with colors, typography, and usage rules.', 1, 6, 120.00, 6, 1, 'Portuguese', 5.00, 1),
(1, 'Brand Identity Manual1', 'Complete visual identity guide with colors, typography, and usage rules.', 1, 6, 120.00, 6, 1, 'Portuguese', 0.00, 0),
(1, 'Brand Identity Manual2', 'Complete visual identity guide with colors, typography, and usage rules.', 1, 6, 120.00, 6, 1, 'Portuguese', 0.00, 0);


INSERT INTO service_images (service_id, media_url) VALUES
(1, 'https://picsum.photos/300?logo1'),
(2, 'https://picsum.photos/300?landing1'),
(3, 'https://picsum.photos/300?card1'),
(4, 'https://picsum.photos/300?website1'),
(5, 'https://picsum.photos/300?template1'),
(6, 'https://picsum.photos/300?landing2'),
(7, 'https://picsum.photos/300?identity1');

INSERT INTO reviews (service_id, client_id, rating, comment) VALUES
(1, 4, 5, 'Excellent service, highly recommended!'),
(1, 3, 5, 'Perfect service, amazing work!'),
(1, 1, 4, 'Loved it!'),
(2, 2, 4, 'Very professional, but could be a bit faster.'),
(3, 4, 5, 'Loved the business card design, it’s exactly what I needed.'),
(4, 2, 3, 'Good work, but took longer than expected.'),
(5, 4, 4, 'Nice templates for Instagram, helped boost my brand.'),
(6, 2, 5, 'Landing page looks great and works perfectly.'),
(7, 4, 5, 'The brand guide was super useful, great attention to detail.'),
(2, 4, 4, 'Clean layout, fits well on mobile devices.'),
(3, 2, 5, 'Super fast delivery and great communication.'),
(1, 2, 5, 'Amazing logo, captured my vision perfectly.');

INSERT INTO conversations (id, service_id, user1_id, user2_id) VALUES
('5_6', 1, 5, 6),
('5_7', 2, 5, 7);

INSERT INTO messages (conversation_id, service_id, sender_id, receiver_id, message) VALUES
('5_6', 1, 6, 5, 'Olá, estou interessada no teu serviço de design.'),
('5_6', 1, 5, 6, 'Olá Ana! Claro, como posso ajudar?');
-- ('5_6', 2, 5, 6, 'Preciso de ajuda com o site da empresa.'),
-- ('5_6', 2, 5, 6, 'Claro, posso marcar uma call para hoje.');

INSERT INTO hirings (service_id, client_id, owner_id) VALUES
(1, 5, 6),
(1, 7, 6),
(3, 5, 6),
(6, 5, 6),
(5, 6, 5),
(4, 6, 5),
(2, 6, 5);