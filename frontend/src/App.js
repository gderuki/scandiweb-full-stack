import React, { Component } from 'react';
import './App.css';
import StickyNavbar from 'components/organisms/StickyNavbar';
import ProductListing from 'components/pages/ProductListing';
import { BrowserRouter, Route, Redirect } from 'react-router-dom';

class App extends Component {
  render() {
    return (
      <BrowserRouter>
        <StickyNavbar />
        <Redirect from="/" to ="/category/women" />
        <Route path="/category/:categoryName" component={ProductListing} />
      </BrowserRouter>
    );
  }
}

export default App;