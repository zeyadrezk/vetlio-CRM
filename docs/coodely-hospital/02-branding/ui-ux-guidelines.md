# 🎨 UI/UX Guidelines - Coodely Hospital

## Design Principles

### 1. Clarity First
- Clear labels and instructions
- Consistent terminology
- Minimal cognitive load

### 2. Accessibility
- WCAG 2.1 AA compliance
- Keyboard navigation support
- Screen reader friendly
- High contrast ratios

### 3. Mobile Responsive
- Mobile-first approach
- Touch-friendly targets (min 44x44px)
- Responsive breakpoints

### 4. Consistent Patterns
- Reuse Filament components
- Consistent spacing
- Predictable interactions

---

## Component Standards

### Buttons

**Primary Button**
- Background: Primary-500
- Text: White
- Padding: 12px 24px
- Border-radius: 6px
- Hover: Primary-600

**Secondary Button**
- Border: 1px solid Primary-500
- Text: Primary-700
- Background: White
- Hover: Primary-50 background

**Danger Button**
- Background: Red-500
- Text: White
- Use for destructive actions

### Forms

**Input Fields**
- Height: 42px
- Border: 1px solid Gray-300
- Focus: 2px Primary-500 ring
- Border-radius: 6px

**Required Fields**
- Red asterisk (*)
- Clear error messages below field

### Tables

**Header**
- Background: Gray-100
- Text: Gray-700 (semibold)
- Border-bottom: 2px solid Gray-200

**Rows**
- Hover: Gray-50
- Striped (optional): Alternate Gray-50

### Status Badges

**Styling**
- Padding: 4px 12px
- Border-radius: 12px (pill shape)
- Font-size: 12px (semibold)
- See color-palette.md for status colors

---

## Spacing System

Use Tailwind's spacing scale:
- xs: 0.5rem (8px)
- sm: 0.75rem (12px)
- md: 1rem (16px)
- lg: 1.5rem (24px)
- xl: 2rem (32px)

---

## Layout

### Dashboard
- Max-width: 2xl (1536px)
- Sidebar: 256px (collapsible)
- Main content: Flex-grow
- Cards: White background, shadow-sm

### Patient Portal
- Top navigation (horizontal)
- Centered content: Max-width 1024px
- Friendly, less dense than staff panel

---

**Document Version**: 1.0
