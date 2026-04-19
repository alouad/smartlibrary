export const booksData = [
  {
    id: 1,
    title: "The Great Adventure",
    author: "Jane Smith",
    category: "Fiction",
    rating: 4.5,
    pages: 342,
    published: 2023,
    isbn: "978-3-16-148410-0",
    cover: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=400",
    description: "An epic journey through uncharted territories where courage meets destiny. Follow the protagonist as they navigate through challenges that test both physical and emotional boundaries.",
    isPremium: false,
    pdfUrl: ""
  },
  {
    id: 2,
    title: "Science Fundamentals",
    author: "Dr. Robert Johnson",
    category: "Science",
    rating: 4.8,
    pages: 520,
    published: 2022,
    isbn: "978-1-23-456789-0",
    cover: "https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&q=80&w=400",
    description: "A comprehensive guide to the fundamental principles of modern science, exploring physics, chemistry, and biology through real-world applications.",
    isPremium: true,
    pdfUrl: "dummy-science.pdf"
  },
  {
    id: 3,
    title: "World History Chronicles",
    author: "Margaret Wilson",
    category: "History",
    rating: 4.6,
    pages: 456,
    published: 2021,
    isbn: "978-0-12-345678-9",
    cover: "https://images.unsplash.com/photo-1461360370896-922624d12aa1?auto=format&fit=crop&q=80&w=400",
    description: "Uncover the defining moments of human history. From ancient civilizations to the modern era, this book provides a detailed look at the events that shaped our world.",
    isPremium: false,
    pdfUrl: ""
  },
  {
    id: 4,
    title: "Modern Programming",
    author: "Alex Chen",
    category: "Technology",
    rating: 4.9,
    pages: 380,
    published: 2024,
    isbn: "978-9-87-654321-0",
    cover: "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400",
    description: "Master the latest programming paradigms and technologies. A practical guide to building scalable, efficient, and modern software applications.",
    isPremium: true,
    pdfUrl: "dummy-programming.pdf"
  },
  {
    id: 5,
    title: "Thinking in Systems",
    author: "Donella Meadows",
    category: "Self-Help",
    rating: 4.7,
    pages: 240,
    published: 2008,
    isbn: "978-1-60-358055-7",
    cover: "https://images.unsplash.com/photo-1589829085413-56de8ae18c73?auto=format&fit=crop&q=80&w=400",
    description: "A brilliant primer bringing systems thinking out of the realm of computers and equations and into the tangible world, illuminating how to create proactive and effective solutions.",
    isPremium: false,
    pdfUrl: ""
  },
  {
    id: 6,
    title: "The Poetry of Reality",
    author: "Richard Dawkins",
    category: "Poetry",
    rating: 4.3,
    pages: 180,
    published: 2011,
    isbn: "978-0-59-306612-6",
    cover: "https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&q=80&w=400",
    description: "Explore the magic of reality through the lens of poetic science, unraveling the universe's greatest mysteries with eloquent verses.",
    isPremium: false,
    pdfUrl: ""
  },
  {
    id: 7,
    title: "Mind over Matter",
    author: "Arthur Schopenhauer",
    category: "Philosophy",
    rating: 4.8,
    pages: 512,
    published: 1818,
    isbn: "978-0-14-044421-5",
    cover: "https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&q=80&w=400",
    description: "A cornerstone of philosophical thought that revolutionized how we look at will, perception, and human existence.",
    isPremium: true,
    pdfUrl: "dummy-philosophy.pdf"
  },
  {
    id: 8,
    title: "Leonardo da Vinci",
    author: "Walter Isaacson",
    category: "Biography",
    rating: 4.9,
    pages: 600,
    published: 2017,
    isbn: "978-1-50-113915-4",
    cover: "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=400",
    description: "The biography of history's most creative genius, connecting his art to his science while offering insights into how to foster innovation today.",
    isPremium: false,
    pdfUrl: ""
  },
  {
    id: 9,
    title: "Future of AI",
    author: "Kai-Fu Lee",
    category: "Technology",
    rating: 4.6,
    pages: 280,
    published: 2018,
    isbn: "978-1-32-853902-8",
    cover: "https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&q=80&w=400",
    description: "An examination of how Artificial Intelligence will drastically change the world, written by a master of the tech industry.",
    isPremium: true,
    pdfUrl: "dummy-ai.pdf"
  },
  {
    id: 10,
    title: "Silent Spring",
    author: "Rachel Carson",
    category: "Science",
    rating: 4.8,
    pages: 400,
    published: 1962,
    isbn: "978-0-61-824906-0",
    cover: "https://images.unsplash.com/photo-1502481851512-e9e2529bfbf9?auto=format&fit=crop&q=80&w=400",
    description: "The classic that launched the environmental movement by exposing the dangers of pesticides, altering the course of history and public policy.",
    isPremium: false,
    pdfUrl: ""
  }
];

export const categories = [
  "Fiction", "Science", "History", "Technology", 
  "Philosophy", "Poetry", "Biography", "Self-Help"
];

export const initialUsersData = [
  { id: 1, name: "Admin User", email: "admin@test.com", role: "admin", isPremium: true, joinDate: "2023-01-15" },
  { id: 2, name: "Author Jane", email: "author@test.com", role: "author", isPremium: false, joinDate: "2023-05-20" },
  { id: 3, name: "Premium Member", email: "premium@test.com", role: "user", isPremium: true, joinDate: "2023-11-05" },
  { id: 4, name: "Regular Reader", email: "user@test.com", role: "user", isPremium: false, joinDate: "2024-02-10" },
  { id: 5, name: "Nouralhouda", email: "alouadnouralhouda@gmail.com", role: "admin", isPremium: true, joinDate: "2024-04-19" },
  { id: 6, name: "Mbrh", email: "mbrh679@gmail.com", role: "admin", isPremium: true, joinDate: "2024-04-19" }
];

export const platformStats = {
  totalUsers: 1254,
  activeSubscriptions: 342,
  totalAuthors: 89,
  totalBooks: 450,
  totalReads: 12500,
  revenue: 3420
};

export const mockAuthorStats = {
  totalReads: 5430,
  averageRating: 4.7,
  downloads: 1205,
  monthlyViews: [120, 150, 180, 220, 300, 250, 400]
};
