import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import styled from 'styled-components'
import store from '../stores'
import { GUTTER } from '~components/Config/src'
import { device } from '~components/Style/src/components/devices'
import { rgba } from 'polished'
import template from '~components/Helper/src/components/template'

interface StyledSysLoadGroupProps {
  isCenter: boolean
}

export const StyledSysLoadGroup = styled.div<StyledSysLoadGroupProps>`
  display: flex;
  align-items: center;
  justify-content: center;
  @media ${device('tablet')} {
    justify-content: ${({ isCenter }) => (isCenter ? 'center' : 'flex-start')};
  }
`

export const StyledSysLoadGroupItem = styled.span`
  margin-right: 0.5rem;
  background: ${({ theme }) => rgba(theme.colorDark, 0.75)};
  color: ${({ theme }) => theme.colorGray};
  padding: calc(${GUTTER} / 10) calc(${GUTTER} / 1.5);
  border-radius: 10rem;
  font-family: Arial Black;
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  box-shadow: inset 0 5px 10px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
  font-weight: 700;

  @media ${device('tablet')} {
    padding: calc(${GUTTER} / 10) ${GUTTER};
  }
`

interface SysLoadGroupProps {
  sysLoad: number[]
  isCenter: boolean
}

export const SysLoadGroup = ({ sysLoad, isCenter }: SysLoadGroupProps) => {
  const minutes = [1, 5, 15]
  const loadHuman = sysLoad.map((load, i) => {
    return {
      id: `${minutes[i]}minAvg`,
      load,
      text: template(gettext('${minute} minute average'), {
        minute: minutes[i],
      }),
    }
  })

  return (
    <StyledSysLoadGroup isCenter={isCenter}>
      {loadHuman.map(({ id, load, text }) => (
        <StyledSysLoadGroupItem key={id} title={text}>
          {load.toFixed(2)}
        </StyledSysLoadGroupItem>
      ))}
    </StyledSysLoadGroup>
  )
}

interface SystemLoadProps {
  isCenter?: boolean
}

@observer
export default class SystemLoad extends Component<SystemLoadProps> {
  public static defaultProps = {
    isCenter: false,
  }

  public render() {
    return (
      <CardGrid name={gettext('System load')} tablet={[1, 1]}>
        <SysLoadGroup
          isCenter={!!this.props.isCenter}
          sysLoad={store.sysLoad}
        />
      </CardGrid>
    )
  }
}
