import React, { Component, ComponentClass } from 'react'
import styled from 'styled-components'
import { observer } from 'mobx-react'
import store from '../stores'
import { ReactNode } from 'react'

const Fieldset = styled.fieldset`
  position: relative;
  border: 5px solid #eee;
  border-radius: 1.5rem;
  background: linear-gradient(#fff, #ffffff80);
  margin-bottom: 1.5rem;
  padding: 1.5rem 0 0;
  box-shadow: -1px -1px 0 rgba(#000, 0.1), 1px 1px 0 hsla(0, 0%, 100%, 0.5),
    inset 1px 1px 0 hsla(0, 0%, 100%, 0.5), inset -1px -1px 0 rgba(#000, 0.1);
`

const Legend = styled.legend`
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: #333;
  padding: 0.5rem 2rem;
  border-radius: 5rem;
  color: #fff;
  margin: 0 auto;
  text-shadow: 0 1px 1px #333;
`

const Body = styled.div``

@observer
class Cards extends Component {
  public render() {
    const { cardsLength, sortedCards } = store

    if (!cardsLength) {
      return null
    }

    return (
      <>
        {sortedCards.map(({ id, title, component: Tag }) => {
          return (
            <Fieldset key={id}>
              <Legend>{title}</Legend>
              <Body>
                <Tag />
              </Body>
            </Fieldset>
          )
        })}
      </>
    )
  }
}

export default Cards
