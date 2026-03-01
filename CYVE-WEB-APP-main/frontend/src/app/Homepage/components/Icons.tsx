import React from 'react';

type IconProps = {
    className?: string;
    width?: number;
    height?: number;
    color?: string;
};

export const RoadmapIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill={color} />
    </svg>
);

export const CalendarIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4ZM19 20H5V10H19V20ZM19 8H5V6H19V8ZM9 14H7V12H9V14ZM13 14H11V12H13V14ZM17 14H15V12H17V14ZM9 18H7V16H9V18ZM13 18H11V16H13V18ZM17 18H15V16H17V18Z" fill={color} />
    </svg>
);

export const ShieldIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 11.99H7V10H12V7.47C13.93 7.96 15.48 9.38 15.93 11.23H12V11.99Z" fill={color} />
    </svg>
);

export const UserIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill={color} />
    </svg>
);

export const TargetIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.58 20 4 16.42 4 12C4 7.58 7.58 4 12 4C16.42 4 20 7.58 20 12C20 16.42 16.42 20 12 20ZM12 6C8.69 6 6 8.69 6 12C6 15.31 8.69 18 12 18C15.31 18 18 15.31 18 12C18 8.69 15.31 6 12 6ZM12 16C9.79 16 8 14.21 8 12C8 9.79 9.79 8 12 8C14.21 8 16 9.79 16 12C16 14.21 14.21 16 12 16Z" fill={color} />
    </svg>
);

export const GamepadIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M21 6H3C1.9 6 1 6.9 1 8V16C1 17.1 1.9 18 3 18H21C22.1 18 23 17.1 23 16V8C23 6.9 22.1 6 21 6ZM6 13.5C5.17 13.5 4.5 12.83 4.5 12C4.5 11.17 5.17 10.5 6 10.5C6.83 10.5 7.5 11.17 7.5 12C7.5 12.83 6.83 13.5 6 13.5ZM9 10.5H11V8.5H9V10.5ZM19 13H17V15H15V13H13V11H15V9H17V11H19V13Z" fill={color} />
    </svg>
);

export const GraduationIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z" fill={color} />
    </svg>
);

export const SecurityIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 11.99H7V10H12V7.47C13.93 7.96 15.48 9.38 15.93 11.23H12V11.99Z" fill={color} />
        <path d="M12 7C14.21 7 16 8.79 16 11H17V11.99H16V14H8V11.99H7V11C7 8.79 8.79 7 12 7ZM10 11H14V9C14 7.9 13.1 7 12 7C10.9 7 10 7.9 10 9V11Z" fill={color === 'currentColor' ? '#000' : 'currentColor'} opacity="0.5" />
    </svg>
);

export const SkullIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C7.58 2 4 5.58 4 10C4 11.96 4.7 13.76 5.87 15.17L3.41 17.63L4.83 19.05L7.29 16.59C8.7 17.76 10.5 18.46 12.46 18.46V22H11.54V18.46C13.5 18.46 15.3 17.76 16.71 16.59L19.17 19.05L20.59 17.63L18.13 15.17C19.3 13.76 20 11.96 20 10C20 5.58 16.42 2 12 2ZM8 11C7.17 11 6.5 10.33 6.5 9.5C6.5 8.67 7.17 8 8 8C8.83 8 9.5 8.67 9.5 9.5C9.5 10.33 8.83 11 8 11ZM16 11C15.17 11 14.5 10.33 14.5 9.5C14.5 8.67 15.17 8 16 8C16.83 8 17.5 8.67 17.5 9.5C17.5 10.33 16.83 11 16 11Z" fill={color} />
    </svg>
);

export const MapPinIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill={color} />
    </svg>
);

export const CheckCircleIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill={color} />
    </svg>
);

export const LibraryIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M4 6H2V20C2 21.1 2.9 22 4 22H18V20H4V6ZM20 2H8C6.9 2 6 2.9 6 4V16C6 17.1 6.9 18 8 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H8V4H20V16ZM10 9H18V11H10V9ZM10 12H15V14H10V12ZM10 5H18V7H10V5Z" fill={color} />
    </svg>
);

export const NoteIcon: React.FC<IconProps> = ({ className, width = 24, height = 24, color = 'currentColor' }) => (
    <svg width={width} height={height} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M3 17.25V21H6.75L17.81 9.94L14.06 6.19L3 17.25ZM20.71 7.04C21.1 6.65 21.1 6.02 20.71 5.63L18.37 3.29C17.98 2.9 17.35 2.9 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04Z" fill={color} />
        <path d="M0 0h24v24H0z" fill="none" />
    </svg>
);
