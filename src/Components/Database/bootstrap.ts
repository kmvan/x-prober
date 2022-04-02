import { CardStore } from '../Card/stores'
import { gettext } from '../Language'
import { Database as component } from './components'
import { DatabaseStore } from './stores'
export const DatabaseBootstrap = (): void => {
  DatabaseStore.enabled &&
    CardStore.addCard({
      id: DatabaseStore.ID,
      title: gettext('Database'),
      tinyTitle: gettext('DB'),
      priority: 600,
      component,
    })
}
