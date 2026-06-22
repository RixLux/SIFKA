### [1] Core Foundation (Global Error Catching)

* [x] **Install `react-error-boundary`:** Add this library via npm/pnpm to catch runtime JavaScript errors, preventing the app from completely crashing into a blank white screen.
* [ ] **Create a `GlobalErrorFallback.tsx` Component:** Design a beautiful error card or page using Tailwind v4 and `lucide-react` icons. Include a "Try Again" or "Refresh Page" button.
* [ ] **Wrap the App Root:** Place the `ErrorBoundary` at the top level of your application (e.g., in `main.tsx` or `App.tsx`, outside your `RouterProvider`).

###  [2] Data Fetching Automation (TanStack Query)

* [ ] **Create a `QueryStateHandler.tsx` Wrapper Component:**
* [ ] Handle the `isLoading` state using a skeleton loader or a Lucide spinner (`animate-spin`).
* [ ] Handle the `isError` state to automatically display the error message coming from Axios/Laravel API.
* [ ] Handle the *Empty State* condition (when the API returns an empty array `[]` or `null`).


* [ ] **Refactor Main Pages:** Replace all manual `if (isLoading)` boilerplate across your data-fetching pages with this new reusable wrapper component.

### [3] Action & Mutation Feedback (User Interactions)

* [ ] **Configure `react-hot-toast`:** Ensure the `<Toaster/>` component is mounted at the root level of your app with optimal positioning (e.g., *top-right* or *bottom-right*).
* [ ] **Migrate to `toast.promise()`:** Scan your codebase for any `useMutation` hooks (form submissions, deletes, updates). Replace manual loading/success/error handlers with a single, clean `toast.promise()` call.

### [4] Reusable Feedback UI Components

* [ ] **Create a `Button` Loading State:** Build a variation of your button component that automatically shows a spinner icon and sets `disabled={isLoading}` when a loading prop is active (perfect for form submit buttons).
* [ ] **Create a Flexible `EmptyState` Component:** Build a generic UI component that accepts `icon`, `title`, and `description` props so it can be reused anywhere (e.g., "No notifications yet", "Your cart is empty", etc.).

---

### Pro-Tip for Your Stack:

Since you have **Radix UI Dialog** and **Zustand** installed, if you ever need global, high-priority feedback (like an account suspension warning or a critical success confirmation that shouldn't be missed in a small toast), you can create a lightweight `useModalStore` in Zustand to trigger a Radix-based modal from anywhere in your app without repeating boilerplate code.
