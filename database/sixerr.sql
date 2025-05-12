PRAGMA foreign_keys = ON;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT CHECK(role IN ('user', 'admin')) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profiles (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    bio TEXT,
    profile_picture TEXT,
    is_freelancer INTEGER DEFAULT 0,
    is_client INTEGER DEFAULT 1
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
    total_reviews INTEGER DEFAULT 0
);

CREATE TABLE service_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    media_url TEXT NOT NULL
);

CREATE TABLE orders_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    freelancer_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    total_price REAL NOT NULL,
    status TEXT CHECK(status IN ('pending', 'in_progress', 'completed', 'cancelled')) DEFAULT 'pending',
    reviewed INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER REFERENCES services(id) ON DELETE CASCADE,
    client_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    rating INTEGER CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(service_id, client_id)
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

CREATE TABLE conversations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user1_id INTEGER REFERENCES users(id),
    user2_id INTEGER REFERENCES users(id),
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    conversation_id INTEGER REFERENCES conversations(id) ON DELETE CASCADE,
    sender_id INTEGER REFERENCES users(id),
    message TEXT NOT NULL,
    read INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

INSERT INTO orders_services (client_id, freelancer_id, service_id, total_price, status) VALUES
(4, 1, 1, 75.00, 'completed'),
(2, 3, 2, 150.00, 'in_progress'),
(3, 1, 1, 75.00, 'in_progress'),
(1, 1, 1, 75.00, 'in_progress');

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

INSERT INTO conversations (user1_id, user2_id) VALUES
(4, 1),
(2, 3);

INSERT INTO messages (conversation_id, sender_id, message) VALUES
(1, 4, 'Olá, estou interessada no teu serviço de design.'),
(1, 1, 'Olá Ana! Claro, como posso ajudar?'),
(2, 2, 'Preciso de ajuda com o site da empresa.'),
(2, 3, 'Claro, posso marcar uma call para hoje.');
