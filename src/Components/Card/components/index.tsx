import { observer } from 'mobx-react-lite'
import { FC, MouseEvent } from 'react'
import { gettext } from '../../Language'
import { NavStore } from '../../Nav/stores'
import { ElevatorNavBody } from '../../Utils/components/elevator-nav'
import { CardStore } from '../stores'
import styles from './styles.module.scss'
const CardArrow: FC<{
  isDown: boolean
  disabled: boolean
  id: string
  handleClick: (id: string) => void
}> = ({ isDown, disabled, id, handleClick }) => {
  const onClick = (e: MouseEvent<HTMLAnchorElement>) => {
    e.preventDefault()
    handleClick(id)
  }
  return (
    <a
      className={styles.arrow}
      title={gettext('Move up')}
      data-disabled={disabled || undefined}
      onClick={onClick}
      href='#'
    >
      {isDown ? '▼' : '▲'}
    </a>
  )
}
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
        return (
          <fieldset className={styles.fieldset} key={id} id={id}>
            <legend className={styles.legend}>
              <CardArrow
                id={id}
                handleClick={moveCardUp}
                isDown={false}
                disabled={i === 0}
              />
              <span className={styles.legendText}>{title}</span>
              <CardArrow
                id={id}
                handleClick={moveCardDown}
                isDown
                disabled={i === enabledCardsLength - 1}
              />
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
