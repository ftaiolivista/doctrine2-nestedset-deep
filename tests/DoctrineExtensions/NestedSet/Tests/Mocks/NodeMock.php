<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL.
 */

namespace DoctrineExtensions\NestedSet\Tests\Mocks;

use DoctrineExtensions\NestedSet\MultipleRootNode;


/**
 * @Entity
 */
class NodeMock implements MultipleRootNode
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $lft;

    /**
     * @Column(type="integer")
     */
    private $rgt;
    
    /**
    * @Column(type="integer")
    */
    private $deep;

    /**
     * @Column(type="string", length="16")
     */
    private $name;

    /**
     * @Column(type="integer")
     */
    private $root;


    /**
     * @OneToOne(targetEntity="RelatedObj", inversedBy="node", fetch="LAZY", cascade={"persist"})
     * @JoinColumn(name="related_id", referencedColumnName="id", nullable="true")
     */
    private $related;

    public function __construct($id, $name=null, $lft=null, $rgt=null, $root=1, $deep=null)
    {
        $this->id  = $id;
        $this->lft = $lft;
        $this->rgt = $rgt;
        $this->deep = $deep;
        $this->root = $root;
        $this->name = $name;
    }

    public function getId() { return $this->id; }

    public function getLeftValue() { return $this->lft; }
    public function setLeftValue($lft) { $this->lft = $lft; }

    public function getRightValue() { return $this->rgt; }
    public function setRightValue($rgt) { $this->rgt = $rgt; }
    
    public function getDeepValue() { return $this->deep; }    
    public function setDeepValue($deep) { $this->deep = $deep; }

    public function getRootValue() { return $this->root; }
    public function setRootValue($root) { $this->root = $root; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function __toString() { return $this->name; }

    public function getRelatedObj() { return $this->related; }
    public function setRelatedObj($related) { $this->related = $related; }
}
