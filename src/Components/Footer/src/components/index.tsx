import React, { Component } from 'react'
import styled from 'styled-components'
import { gettext } from '~components/Language/src'
import { template } from 'lodash-es'
import BootstrapStore from '~components/Bootstrap/src/stores'
import store from '../stores'
import { COLOR_DARK, GUTTER } from '~components/Config/src'
import formatBytes from '~components/Helper/src/components/format-bytes'
import { device } from '~components/Style/src/components/devices'

const StyledFooter = styled.div`
  background: ${COLOR_DARK};
  color: #ccc;
  width: 100%;
  border-radius: 10rem;
  text-align: center;
  padding: calc(${GUTTER} / 2) ${GUTTER};
  margin: calc(${GUTTER} * 1.5) auto;
  word-break: normal;

  @media ${device('tablet')} {
    width: 60%;
  }

  a {
    color: #fff;
  }
`

class Footer extends Component {
  public render() {
    const { appName, appUrl, authorName, authorUrl } = BootstrapStore
    const { memUsage, time } = store.conf

    return (
      <StyledFooter
        dangerouslySetInnerHTML={{
          __html: template(
            gettext(
              'Generator <%= appName %> / Author <%= authorName %> / <%= memUsage %> / <%= time %>ms'
            )
          )({
            appName: `<a href="${appUrl}" target="_blank">${appName}</a>`,
            authorName: `<a href="${authorUrl}" target="_blank">${authorName}</a>`,
            memUsage: formatBytes(memUsage),
            time: (time * 1000).toFixed(2),
          }),
        }}
      />
    )
  }
}

export default Footer
