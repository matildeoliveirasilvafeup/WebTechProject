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
    hiring_id INTEGER,
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
    status TEXT NOT NULL DEFAULT 'Pending' CHECK (status IN ('Pending', 'Accepted', 'Rejected', 'Cancelled', 'Completed', 'Closed', 'Disabled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP
);

CREATE TABLE custom_offers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hiring_id INTEGER,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    price REAL NOT NULL CHECK(price >= 0),
    delivery_time INTEGER NOT NULL CHECK(delivery_time >= 1),
    number_of_revisions INTEGER DEFAULT 1 CHECK(number_of_revisions >= 0),
    status TEXT DEFAULT 'Pending' CHECK(status IN ('Pending', 'Accepted', 'Rejected', 'Cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP,

    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);




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
