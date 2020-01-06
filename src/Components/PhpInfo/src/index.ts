import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

CardStore.addCard({
  id: store.ID,
  title: gettext('PHP Information'),
  tinyTitle: gettext('PHP'),
  priority: 400,
  component,
})
