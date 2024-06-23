import React, { Component } from 'react';
import './App.css';
import StickyNavbar from 'components/organisms/StickyNavbar';
import ProductListing from 'components/pages/ProductListing';

class App extends Component {
  state = { selectedCategory: 'women' };

  handleCategorySelect = (category) => {
    this.setState({ selectedCategory: category });
  };

  render() {

    return (
      <div className="App">
        <StickyNavbar onCategorySelect={this.handleCategorySelect} />
        <div className="productGrid">
          <ProductListing category={this.state.selectedCategory} />
        </div>
      </div>
    );
  }
}

export default App;