// Node modules
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { ApolloClient, InMemoryCache, ApolloProvider } from '@apollo/client';

// Custom Modules
import App from './App';

// Styles/CSS
import './index.css';

const client = new ApolloClient({
  uri: process.env.API_URL || 'http://localhost/api/',
  cache: new InMemoryCache({
    typePolicies: {
      Attribute: {
        keyFields: (attribute) => {
          let key = `Attribute:${attribute.id}:${attribute.value}`;

          if (attribute.items) {
            const itemIds = attribute.items.map(item => item.id).join(':');
            key += `:${itemIds}`;
          }

          return key;
        },
      },
    },
  }),
});

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <BrowserRouter>
      <ApolloProvider client={client}>
        <App />
      </ApolloProvider>
    </BrowserRouter>
  </React.StrictMode>
);