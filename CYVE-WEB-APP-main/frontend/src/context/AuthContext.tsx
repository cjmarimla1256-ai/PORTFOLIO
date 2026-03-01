'use client';

import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';

interface User {
    id: string | number;
    email: string;
    name: string;
    role: string;
}

interface AuthContextType {
    user: User | null;
    isAuthenticated: boolean;
    login: (email: string, password: string, remember?: boolean) => Promise<{ success: boolean; message: string }>;
    signup: (name: string, email: string, password: string) => Promise<{ success: boolean; message: string }>;
    logout: () => void;
    loading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

// API Configuration
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost/ARZAGA/CYVE-WEB-APP-main/backend/api';
const CURRENT_USER_KEY = 'cyve_current_user';

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        // Check if user is already logged in (persistence)
        const storedUser = localStorage.getItem(CURRENT_USER_KEY);
        if (storedUser) {
            try {
                setUser(JSON.parse(storedUser));
            } catch (error) {
                console.error('Error parsing stored user:', error);
                localStorage.removeItem(CURRENT_USER_KEY);
            }
        }
        setLoading(false);
    }, []);

    const login = async (email: string, password: string, remember: boolean = false): Promise<{ success: boolean; message: string }> => {
        try {
            const response = await fetch(`${API_BASE_URL}/login.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password }),
            });

            const data = await response.json();

            if (data.success) {
                const userObj: User = {
                    id: data.user.id,
                    email: data.user.email,
                    name: data.user.name,
                    role: data.user.role || 'user'
                };

                setUser(userObj);
                localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(userObj));
                return { success: true, message: data.message || 'Login successful!' };
            } else {
                return { success: false, message: data.message || 'Invalid credentials.' };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'Could not connect to the authentication server. Ensure XAMPP is running.' };
        }
    };

    const signup = async (name: string, email: string, password: string): Promise<{ success: boolean; message: string }> => {
        try {
            const response = await fetch(`${API_BASE_URL}/signup.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, email, password }),
            });

            const data = await response.json();

            if (data.success) {
                // After successful signup, we can automatically log them in
                // using the user data returned or the credentials
                return login(email, password);
            } else {
                return { success: false, message: data.message || 'Registration failed.' };
            }
        } catch (error) {
            console.error('Signup error:', error);
            return { success: false, message: 'Could not connect to the registration server. Ensure XAMPP is running.' };
        }
    };

    const logout = async () => {
        try {
            // Optional: call logout API to clear PHP session
            await fetch(`${API_BASE_URL}/logout.php`);
        } catch (e) {
            console.warn('Logout API call failed, continuing with local cleanup');
        }

        setUser(null);
        localStorage.removeItem(CURRENT_USER_KEY);
        window.location.href = '/';
    };

    return (
        <AuthContext.Provider value={{ user, isAuthenticated: !!user, login, signup, logout, loading }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}

