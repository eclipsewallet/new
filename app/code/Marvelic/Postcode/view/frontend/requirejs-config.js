var config = {
  map: {
    '*': {      
      thailand: 'Marvelic_Postcode/js/Thailand',
      thaieng: 'Marvelic_Postcode/js/ThaiAddressEnTh',
      // 'Magento_Checkout/js/view/billing-address': 'Marvelic_Postcode/js/view/billing-address'
    }
  },
  paths: {
    jql: 'Marvelic_Postcode/js/dependencies/JQL.min',
    typeahead: 'Marvelic_Postcode/js/dist/typeahead.jquery',
    thailand: 'Marvelic_Postcode/js/Thailand',
    thaieng: 'Marvelic_Postcode/js/ThaiAddressEnTh'
  },
  shim: {
    'jql': {
      exports: 'JQL'
    },
    'typeahead': {
      deps: ['jquery']
    },
    'thailand': {
      deps: ['jquery','jql','typeahead'],
      exports: 'Thailand'
    },
    'thaieng': {
      deps: ['jquery','jql','typeahead'],
      exports: 'ThaiAddressEnTh'
    }
  },
    config: {
      mixins: {
        'Magento_Ui/js/lib/validation/validator': {
            'Marvelic_Postcode/js/validation-mixin': true
        }
      }
    }
};