import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { gettext } from '../../Language'
import { NavStore } from '../../Nav/stores'
import { ElevatorNavBody } from '../../Utils/components/elevator-nav'
import { CardStore } from '../stores'
import styles from './styles.module.scss'
export const Cards: FC = observer(() => {
  const {
    cardsLength,
    enabledCards,
    enabledCardsLength,
    moveCardDown,
    moveCardUp,
  } = CardStore
  if (!cardsLength) {
    return null
  }
  return (
    <ElevatorNavBody id='innCard' setActiveIndex={NavStore.setActiveIndex}>
      {enabledCards.map(({ id, title, component: Tag }, i) => {
        const upArrow = (
          <a
            className={styles.arrow}
            title={gettext('Move up')}
            data-disabled={i === 0 || undefined}
            onClick={(e) => moveCardUp(e, id)}
            href='#'
          >
            ▲
          </a>
        )
        const downArrow = (
          <a
            className={styles.arrow}
            title={gettext('Move down')}
            data-disabled={i === enabledCardsLength - 1 || undefined}
            onClick={(e) => moveCardDown(e, id)}
            href='#'
          >
            ▼
          </a>
        )
        return (
          <fieldset className={styles.fieldset} key={id} id={id}>
            <legend className={styles.legend}>
              {upArrow}
              <span className={styles.legendText}>{title}</span>
              {downArrow}
            </legend>
            <div className={styles.body}>
              <Tag />
            </div>
          </fieldset>
        )
      })}
    </ElevatorNavBody>
  )
})
